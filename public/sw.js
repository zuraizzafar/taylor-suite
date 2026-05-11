'use strict';

// ─── Cache names ──────────────────────────────────────────────────────────────
const CACHE_VER    = 1;
const CACHE_STATIC = `suit-tailor-static-v${CACHE_VER}`;
const CACHE_PAGES  = `suit-tailor-pages-v${CACHE_VER}`;
const OFFLINE_URL  = '/offline.html';

// ─── IDB constants ───────────────────────────────────────────────────────────
const IDB_NAME    = 'suit-tailor-pwa';
const IDB_VERSION = 1;
const IDB_STORES  = ['customers','orders','suits','workers','branches','stitch_types','mutations','meta'];

// ─── Install ─────────────────────────────────────────────────────────────────
self.addEventListener('install', event => {
    event.waitUntil(
        caches.open(CACHE_STATIC)
            .then(c => c.add(OFFLINE_URL))
            .then(() => self.skipWaiting())
    );
});

// ─── Activate ────────────────────────────────────────────────────────────────
self.addEventListener('activate', event => {
    const keep = new Set([CACHE_STATIC, CACHE_PAGES]);
    event.waitUntil(
        caches.keys()
            .then(keys => Promise.all(
                keys.filter(k => !keep.has(k)).map(k => caches.delete(k))
            ))
            .then(() => self.clients.claim())
    );
});

// ─── Fetch ───────────────────────────────────────────────────────────────────
self.addEventListener('fetch', event => {
    const { request } = event;
    const url = new URL(request.url);

    // Only same-origin GETs
    if (request.method !== 'GET') return;
    if (url.origin !== self.location.origin) return;

    // Sync API — always network, never cache
    if (url.pathname.includes('/sync/')) return;

    // Static assets (scripts, styles, images, fonts) → cache-first
    const dest = request.destination;
    if (['script','style','image','font','manifest'].includes(dest) ||
        url.pathname.match(/\.(css|js|woff2?|png|jpg|jpeg|gif|svg|ico|webp)(\?.*)?$/)) {
        event.respondWith(cacheFirst(request));
        return;
    }

    // HTML documents → network-first with offline fallback
    event.respondWith(networkFirst(request));
});

async function cacheFirst(request) {
    const cached = await caches.match(request);
    if (cached) return cached;
    try {
        const response = await fetch(request);
        if (response.ok) {
            const cache = await caches.open(CACHE_STATIC);
            cache.put(request, response.clone());
        }
        return response;
    } catch {
        return new Response('', { status: 408 });
    }
}

async function networkFirst(request) {
    const cache = await caches.open(CACHE_PAGES);
    try {
        const response = await fetch(request);
        if (response.ok) {
            cache.put(request, response.clone());
        }
        return response;
    } catch {
        const cached = await cache.match(request);
        if (cached) return cached;
        // Fall back to offline page
        return caches.match(OFFLINE_URL);
    }
}

// ─── Background Sync ─────────────────────────────────────────────────────────
self.addEventListener('sync', event => {
    if (event.tag === 'offline-queue') {
        event.waitUntil(replayQueue());
    }
});

async function replayQueue() {
    let db;
    try {
        db = await idbOpen();
        const mutations = await idbGetAll(db, 'mutations');
        let synced = 0;

        for (const m of mutations) {
            try {
                const r = await fetch('/sync/push', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: JSON.stringify(m),
                    credentials: 'same-origin',
                });

                if (r.ok) {
                    await idbDelete(db, 'mutations', m.id);
                    synced++;
                }
            } catch { /* will retry */ }
        }

        if (synced > 0) {
            const clients = await self.clients.matchAll({ type: 'window' });
            clients.forEach(c => c.postMessage({ type: 'SYNC_COMPLETE', count: synced }));
        }
    } finally {
        db?.close();
    }
}

// ─── Minimal IDB helpers (used in SW) ────────────────────────────────────────
function idbOpen() {
    return new Promise((resolve, reject) => {
        const req = indexedDB.open(IDB_NAME, IDB_VERSION);
        req.onsuccess  = () => resolve(req.result);
        req.onerror    = () => reject(req.error);
        req.onupgradeneeded = e => {
            const db = e.target.result;
            for (const name of IDB_STORES) {
                if (!db.objectStoreNames.contains(name)) {
                    if (name === 'mutations') {
                        db.createObjectStore(name, { keyPath: 'id', autoIncrement: true });
                    } else if (name === 'meta') {
                        db.createObjectStore(name, { keyPath: 'key' });
                    } else {
                        db.createObjectStore(name, { keyPath: 'id' });
                    }
                }
            }
        };
    });
}

function idbGetAll(db, storeName) {
    return new Promise((resolve, reject) => {
        const req = db.transaction(storeName, 'readonly').objectStore(storeName).getAll();
        req.onsuccess = () => resolve(req.result);
        req.onerror   = () => reject(req.error);
    });
}

function idbDelete(db, storeName, key) {
    return new Promise((resolve, reject) => {
        const req = db.transaction(storeName, 'readwrite').objectStore(storeName).delete(key);
        req.onsuccess = () => resolve();
        req.onerror   = () => reject(req.error);
    });
}
