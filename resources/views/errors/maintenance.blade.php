<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Service Unavailable</title>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            background: #07090f;
            color: #94a3b8;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }

        .card {
            background: #111827;
            border: 1px solid #1e293b;
            border-radius: 1.25rem;
            padding: 3rem 3.5rem;
            max-width: 480px;
            width: 100%;
            text-align: center;
        }

        .icon {
            width: 64px;
            height: 64px;
            background: #1e293b;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.75rem;
        }

        .icon svg {
            width: 30px;
            height: 30px;
            stroke: #64748b;
        }

        h1 {
            color: #e2e8f0;
            font-size: 1.5rem;
            font-weight: 600;
            letter-spacing: -0.02em;
            margin-bottom: 0.75rem;
        }

        p {
            font-size: 0.9rem;
            line-height: 1.6;
            color: #64748b;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: #1e293b;
            border: 1px solid #334155;
            border-radius: 999px;
            padding: 0.35rem 0.85rem;
            font-size: 0.7rem;
            font-weight: 600;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: #94a3b8;
            margin-bottom: 1.75rem;
        }

        .dot {
            width: 6px;
            height: 6px;
            background: #f59e0b;
            border-radius: 50%;
            animation: pulse 2s ease-in-out infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.3; }
        }
    </style>
</head>
<body>
    <div class="card">
        <div class="icon">
            <svg viewBox="0 0 24 24" fill="none" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
            </svg>
        </div>

        <div class="badge">
            <span class="dot"></span>
            503 — Service Unavailable
        </div>

        <h1>System Maintenance</h1>
        <p>The system is currently undergoing scheduled maintenance.<br>Please check back later or contact support.</p>
    </div>
</body>
</html>
