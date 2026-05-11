/**
 * pwa.js — Service Worker registration, IndexedDB, sync, offline queue, UI
 *
 * Usage in Blade forms for offline support:
 *   <form data-offline="true" data-offline-type="create_customer" data-offline-redirect="/customers">
 */

'use strict';

// ─── Constants ───────────────────────────────────────────────────────────────
const IDB_NAME    = 'suit-tailor-pwa';
const IDB_VERSION = 1;
const IDB_STORES  = ['customers','orders','suits','workers','branches','stitch_types','mutations','meta'];

// ─── Service Worker registration ─────────────────────────────────────────────
if ('serviceWorker' in navigator) {
    window.addEventListener('load', async () => {
        try {
            const reg = await navigator.serviceWorker.register('/sw.js', { scope: '/' });

            // Listen for SW → page messages
            navigator.serviceWorker.addEventListener('message', e => {
                if (e.data?.type === 'SYNC_COMPLETE') {
                    const n = e.data.count;
                    showToast(`✅ ${n} offline change${n !== 1 ? 's' : ''} synced`, 'success');
                    refreshPendingBadge();
                }
            });
        } catch (err) {
            console.warn('[PWA] SW registration failed:', err);
        }
    });
}

// ─── Install prompt ───────────────────────────────────────────────────────────
let deferredInstall = null;

window.addEventListener('beforeinstallprompt', e => {
    e.preventDefault();
    deferredInstall = e;
    document.getElementById('pwa-install-btn')?.classList.remove('hidden');
});

document.addEventListener('click', async e => {
    if (e.target.closest('#pwa-install-btn')) {
        if (!deferredInstall) return;
        deferredInstall.prompt();
        const { outcome } = await deferredInstall.userChoice;
        if (outcome === 'accepted') {
            document.getElementById('pwa-install-btn')?.classList.add('hidden');
        }
        deferredInstall = null;
    }
});

window.addEventListener('appinstalled', () => {
    document.getElementById('pwa-install-btn')?.classList.add('hidden');
    deferredInstall = null;
    showToast('✅ App installed!', 'success');
});

// ─── Online / offline indicator ───────────────────────────────────────────────
function updateNetworkStatus() {
    const bar = document.getElementById('offline-bar');
    if (!bar) return;
    if (navigator.onLine) {
        bar.classList.add('hidden');
        triggerSync();       // replay queue when reconnected
        maybeSyncData();     // refresh local data
    } else {
        bar.classList.remove('hidden');
    }
}

window.addEventListener('online',  updateNetworkStatus);
window.addEventListener('offline', updateNetworkStatus);

// ─── IndexedDB helpers ────────────────────────────────────────────────────────
function dbOpen() {
    return new Promise((resolve, reject) => {
        const req = indexedDB.open(IDB_NAME, IDB_VERSION);
        req.onsuccess  = () => resolve(req.result);
        req.onerror    = () => reject(req.error);
        req.onupgradeneeded = e => {
            const db = e.target.result;
            for (const name of IDB_STORES) {
                if (!db.objectStoreNames.contains(name)) {
                    if (name === 'mutations')     db.createObjectStore(name, { keyPath: 'id', autoIncrement: true });
                    else if (name === 'meta')     db.createObjectStore(name, { keyPath: 'key' });
                    else                          db.createObjectStore(name, { keyPath: 'id' });
                }
            }
        };
    });
}

async function dbPutAll(storeName, items) {
    if (!Array.isArray(items) || items.length === 0) return;
    const db = await dbOpen();
    return new Promise((resolve, reject) => {
        const tx    = db.transaction(storeName, 'readwrite');
        const store = tx.objectStore(storeName);
        items.forEach(item => store.put(item));
        tx.oncomplete = resolve;
        tx.onerror    = () => reject(tx.error);
    });
}

async function dbGetAll(storeName) {
    const db = await dbOpen();
    return new Promise((resolve, reject) => {
        const req = db.transaction(storeName, 'readonly').objectStore(storeName).getAll();
        req.onsuccess = () => resolve(req.result ?? []);
        req.onerror   = () => reject(req.error);
    });
}

async function dbAdd(storeName, item) {
    const db = await dbOpen();
    return new Promise((resolve, reject) => {
        const req = db.transaction(storeName, 'readwrite').objectStore(storeName).add(item);
        req.onsuccess = () => resolve(req.result);
        req.onerror   = () => reject(req.error);
    });
}

async function dbDelete(storeName, key) {
    const db = await dbOpen();
    return new Promise((resolve, reject) => {
        const req = db.transaction(storeName, 'readwrite').objectStore(storeName).delete(key);
        req.onsuccess = () => resolve();
        req.onerror   = () => reject(req.error);
    });
}

async function dbGetMeta(key) {
    const db = await dbOpen();
    return new Promise((resolve, reject) => {
        const req = db.transaction('meta', 'readonly').objectStore('meta').get(key);
        req.onsuccess = () => resolve(req.result?.value ?? null);
        req.onerror   = () => reject(req.error);
    });
}

async function dbSetMeta(key, value) {
    const db = await dbOpen();
    return new Promise((resolve, reject) => {
        const req = db.transaction('meta', 'readwrite').objectStore('meta').put({ key, value });
        req.onsuccess = () => resolve();
        req.onerror   = () => reject(req.error);
    });
}

// ─── Data sync (pull all records from server → IndexedDB) ────────────────────
async function syncDataFromServer() {
    if (!navigator.onLine) return;
    try {
        const res = await fetch('/sync/pull', {
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
            credentials: 'same-origin',
        });
        if (!res.ok) return;
        const data = await res.json();

        await Promise.all([
            dbPutAll('customers',     data.customers    ?? []),
            dbPutAll('orders',        data.orders       ?? []),
            dbPutAll('suits',         data.suits        ?? []),
            dbPutAll('workers',       data.workers      ?? []),
            dbPutAll('branches',      data.branches     ?? []),
            dbPutAll('stitch_types',  data.stitch_types ?? []),
        ]);

        await dbSetMeta('synced_at', data.synced_at);
        showSyncTimestamp(data.synced_at);
    } catch (err) {
        console.warn('[PWA] Sync pull failed:', err);
    }
}

async function maybeSyncData() {
    const lastSync  = await dbGetMeta('synced_at');
    const threshold = new Date(Date.now() - 5 * 60 * 1000).toISOString(); // 5 min
    if (!lastSync || lastSync < threshold) {
        await syncDataFromServer();
    }
}

function showSyncTimestamp(iso) {
    const el = document.getElementById('sync-timestamp');
    if (!el || !iso) return;
    el.textContent = 'Synced ' + new Date(iso).toLocaleTimeString();
}

// ─── Offline write queue ──────────────────────────────────────────────────────
async function queueMutation(type, payload) {
    const id = await dbAdd('mutations', {
        type,
        payload,
        client_id:  crypto.randomUUID(),
        created_at: new Date().toISOString(),
    });
    await refreshPendingBadge();
    return id;
}

async function refreshPendingBadge() {
    const mutations = await dbGetAll('mutations');
    const count = mutations.length;

    const badge = document.getElementById('sync-badge');
    if (badge) {
        badge.textContent = count;
        badge.classList.toggle('hidden', count === 0);
    }

    const syncBtn = document.getElementById('sync-now-btn');
    if (syncBtn) {
        syncBtn.querySelector('.sync-count')?.remove();
        if (count > 0) {
            const span = document.createElement('span');
            span.className = 'sync-count';
            span.textContent = ` (${count})`;
            syncBtn.appendChild(span);
        }
    }
}

// ─── Trigger Background Sync (or fallback manual sync) ────────────────────────
async function triggerSync() {
    if (!navigator.onLine) return;

    if ('serviceWorker' in navigator && 'SyncManager' in window) {
        const reg = await navigator.serviceWorker.ready;
        await reg.sync.register('offline-queue').catch(() => {});
    } else {
        await manualReplay();
    }
}

async function manualReplay() {
    const mutations = await dbGetAll('mutations');
    let synced = 0;

    for (const m of mutations) {
        try {
            const r = await fetch('/sync/push', {
                method: 'POST',
                headers: {
                    'Content-Type':  'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body:        JSON.stringify(m),
                credentials: 'same-origin',
            });

            if (r.ok) {
                await dbDelete('mutations', m.id);
                synced++;
            }
        } catch { /* retry later */ }
    }

    if (synced > 0) {
        showToast(`✅ ${synced} change${synced !== 1 ? 's' : ''} synced`, 'success');
        await refreshPendingBadge();
    }
}

// ─── Sync-now button ──────────────────────────────────────────────────────────
document.addEventListener('click', async e => {
    if (!e.target.closest('#sync-now-btn')) return;
    if (!navigator.onLine) { showToast('No internet connection', 'error'); return; }
    await syncDataFromServer();
    await manualReplay();
    showToast('✅ Synced', 'success');
});

// ─── Form interception (offline-capable forms) ────────────────────────────────
// Add to any form: data-offline="true" data-offline-type="create_customer"
document.addEventListener('submit', async e => {
    const form = e.target.closest('form[data-offline]');
    if (!form || navigator.onLine) return;         // only intercept when offline

    e.preventDefault();

    const type    = form.dataset.offlineType;
    const payload = Object.fromEntries(
        [...new FormData(form).entries()].filter(([k]) => k !== '_token' && k !== '_method')
    );

    await queueMutation(type, payload);

    showToast('📴 Saved offline — will sync when back online', 'warning', 5000);

    const redirect = form.dataset.offlineRedirect;
    if (redirect) {
        setTimeout(() => { window.location.href = redirect; }, 600);
    }
});

// ─── Toast notifications ──────────────────────────────────────────────────────
function showToast(message, type = 'info', duration = 3500) {
    const container = document.getElementById('toast-container');
    if (!container) return;

    const colors = {
        success: 'bg-green-600',
        warning: 'bg-amber-500',
        error:   'bg-red-600',
        info:    'bg-blue-600',
    };

    const el = document.createElement('div');
    el.className = `${colors[type] ?? colors.info} text-white text-sm font-medium px-4 py-3 rounded-xl shadow-lg max-w-xs transition-all duration-300`;
    el.textContent = message;
    container.appendChild(el);

    setTimeout(() => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(-8px)';
        setTimeout(() => el.remove(), 300);
    }, duration);
}

// ─── Expose globally so Blade pages can call them ─────────────────────────────
window.pwa = { showToast, queueMutation, dbGetAll, syncDataFromServer };

// ─── Init ─────────────────────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
    updateNetworkStatus();
    refreshPendingBadge();
    maybeSyncData();
});
