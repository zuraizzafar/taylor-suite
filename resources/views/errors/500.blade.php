@php
    // ── Gather error details ──────────────────────────────────────────────────
    $hasException = isset($exception) && $exception !== null;

    $errorClass   = $hasException ? get_class($exception)          : 'Server Error';
    $errorMessage = $hasException ? $exception->getMessage()       : 'An unexpected error occurred.';
    $errorFile    = $hasException ? $exception->getFile()          : '';
    $errorLine    = $hasException ? $exception->getLine()          : '';
    $errorTrace   = $hasException ? $exception->getTraceAsString() : '';

    // ── Read last 150 lines from laravel.log ─────────────────────────────────
    $logPath    = storage_path('logs/laravel.log');
    $logSnippet = '';
    if (file_exists($logPath) && is_readable($logPath)) {
        $allLines   = file($logPath, FILE_IGNORE_NEW_LINES);
        $logSnippet = implode("\n", array_slice($allLines, -150));
    }

    // ── Build the full text blob (for copy / download) ────────────────────────
    $appUrl  = config('app.url', request()->getSchemeAndHttpHost());
    $ts      = now()->format('Y-m-d H:i:s');

    $fullLog = <<<TEXT
    === Taylor Suite Error Report ===
    Time   : {$ts}
    App    : {$appUrl}
    Error  : {$errorClass}
    Message: {$errorMessage}
    File   : {$errorFile}:{$errorLine}

    === Stack Trace ===
    {$errorTrace}

    === Recent Log (last 150 lines) ===
    {$logSnippet}
    TEXT;

    // ── WhatsApp ──────────────────────────────────────────────────────────────
    $rawWa      = env('SUPPORT_WHATSAPP', '923045511998');
    $waNumber   = preg_replace('/[^0-9]/', '', $rawWa);
    $waText     = "🚨 *Error on {$appUrl}*\n"
                . "⏰ {$ts}\n"
                . "❌ {$errorClass}\n"
                . substr($errorMessage, 0, 300)
                . "\n\n_(Full log copied/downloaded separately)_";
    $waUrl      = 'https://wa.me/' . $waNumber . '?text=' . rawurlencode($waText);
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Oops! | {{ config('app.name') }}</title>
<style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    body {
        font-family: ui-sans-serif, system-ui, sans-serif;
        background: #07090f;
        color: #e2e8f0;
        min-height: 100vh;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 24px;
    }
    @keyframes ledTravel {
        0%   { left: 25%;   top: 15%; }
        25%  { left: 80%; top: 20%; }
        50%  { left: 80%; top: 80%; }
        75%  { left: 20%;   top: 80%; }
        100% { left: 25%;   top: 15%; }
    }
    .card-wrap {
        position: relative;
        max-width: 560px;
        width: 100%;
    }
    .card-wrap::before {
        content: '';
        position: absolute;
        width: 200px;
        height: 200px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(255,200,200,0.98) 0%, rgba(239,68,68,0.7) 30%, rgba(220,38,38,0.2) 60%, transparent 78%);
        pointer-events: none;
        left: 25%;
        top: 15%;
        transform: translate(-50%, -50%);
        animation: ledTravel 3s ease-in-out infinite;
        filter: blur(100px);
        z-index: -1;
    }
    .card-wrap::after {
        content: '';
        position: absolute;
        width: 200px;
        height: 200px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(255,200,200,0.98) 0%, rgba(68, 205, 239, 0.7) 30%, rgba(56, 38, 220, 0.2) 60%, transparent 78%);
        pointer-events: none;
        left: 25%;
        top: 15%;
        transform: translate(-50%, -50%);
        animation: ledTravel 3s ease-in-out infinite;
        animation-delay: 1.5s;
        filter: blur(100px);
        z-index: -1;
    }
    .card {
        position: relative;
        z-index: 1;
        background: #111827;
        border: 1px solid #1f2937;
        border-radius: 20px;
        padding: 48px 44px;
        width: 100%;
        text-align: center;
    }
    .error-badge {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        background: rgba(239,68,68,0.12);
        border: 1px solid rgba(239,68,68,0.4);
        color: #f87171;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 0.06em;
        text-transform: uppercase;
        padding: 4px 14px;
        border-radius: 999px;
        margin-bottom: 18px;
    }
    .error-badge .dot {
        width: 7px; height: 7px; border-radius: 50%;
        background: #ef4444;
        box-shadow: 0 0 6px 2px rgba(239,68,68,0.7);
        display: inline-block;
    }
    .icon { font-size: 56px; margin-bottom: 20px; }
    h1 { font-size: 24px; font-weight: 800; color: #f8fafc; margin-bottom: 10px; }
    .subtitle { font-size: 14px; color: #94a3b8; line-height: 1.7; margin-bottom: 32px; }
    .actions { display: flex; flex-wrap: wrap; gap: 10px; justify-content: center; margin-bottom: 28px; }
    .btn {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        border: none;
        border-radius: 9px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        padding: 10px 18px;
        text-decoration: none;
        transition: opacity 0.15s;
    }
    .btn:hover { opacity: 0.82; }
    .btn-copy     { background: #3b82f6; color: #fff; }
    .btn-download { background: #6366f1; color: #fff; }
    .btn-whatsapp { background: #16a34a; color: #fff; }
    .btn-home     { background: #334155; color: #f1f5f9; }
    .copied-msg { font-size: 12px; color: #4ade80; display: none; align-items: center; gap: 4px; width: 100%; justify-content: center; }
    .support-note { font-size: 11px; color: #475569; border-top: 1px solid #1f2937; padding-top: 20px; line-height: 1.6; }
</style>
</head>
<body>

{{-- Hidden full log — not shown to user --}}
<textarea id="full-log-data" style="display:none;position:absolute;left:-9999px">{{ $fullLog }}</textarea>

<div class="card-wrap">
<div class="card">
    <div class="icon">⚠️</div>
    <div class="error-badge"><span class="dot"></span> 500 &mdash; Internal Server Error</div>
    <h1>Something Went Wrong</h1>
    <p class="subtitle">
        A server error occurred on our end — it is not your fault.<br>
        Please send the error report below so we can fix it as quickly as possible.
    </p>

    <div class="actions">
        <a class="btn btn-home" href="{{ url('/') }}">
            🏠 Back to Dashboard
        </a>
        <button class="btn btn-copy" onclick="copyLog()">
            📋 Copy Error Report
        </button>
        <button class="btn btn-download" onclick="downloadLog()">
            💾 Download Report
        </button>
        <a class="btn btn-whatsapp" href="{{ $waUrl }}" target="_blank" rel="noopener">
            📲 Send via WhatsApp
        </a>
        <span class="copied-msg" id="copied-msg">✅ Copied!</span>
    </div>

    <div class="support-note">
        {{ config('app.name') }} &nbsp;·&nbsp; {{ now()->format('d M Y, H:i') }}
    </div>
</div>
</div>

<script>
    function getLog() {
        return document.getElementById('full-log-data').value;
    }

    function copyLog() {
        var log = getLog();
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(log).then(function() {
                showCopied();
            }).catch(function() {
                fallbackCopy(log);
            });
        } else {
            fallbackCopy(log);
        }
    }

    function fallbackCopy(text) {
        var ta = document.createElement('textarea');
        ta.value = text;
        ta.style.position = 'fixed';
        ta.style.opacity = '0';
        document.body.appendChild(ta);
        ta.focus();
        ta.select();
        try { document.execCommand('copy'); showCopied(); } catch(e) {}
        document.body.removeChild(ta);
    }

    function showCopied() {
        var el = document.getElementById('copied-msg');
        el.style.display = 'inline-flex';
        setTimeout(function() { el.style.display = 'none'; }, 3000);
    }

    function downloadLog() {
        var log  = getLog();
        var blob = new Blob([log], { type: 'text/plain' });
        var url  = URL.createObjectURL(blob);
        var a    = document.createElement('a');
        a.href     = url;
        a.download = 'error-{{ now()->format("Y-m-d_His") }}.txt';
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        URL.revokeObjectURL(url);
    }
</script>

</body>
</html>
