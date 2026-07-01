@php $isRtl = app()->getLocale() === 'ur'; $dir = $isRtl ? 'rtl' : 'ltr'; @endphp
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ $dir }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'The Suit Tailor')</title>
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#1e40af">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="SuitTailor">
    <link rel="apple-touch-icon" href="/icons/icon.svg">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @if($isRtl)
    <link href="https://fonts.googleapis.com/css2?family=Noto+Nastaliq+Urdu:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        /* Base: Inter for all elements so Latin/numbers always look clean */
        [dir=rtl], [dir=rtl] * {
            font-family: 'Inter', ui-sans-serif, system-ui, -apple-system, 'Segoe UI', Roboto, Arial, sans-serif !important;
        }
        /* Apply Nastaliq for page content (nav, tables, cards, headings in main) */
        [dir=rtl] aside *,
        [dir=rtl] main *,
        [dir=rtl] header h2 {
            font-family: 'Noto Nastaliq Urdu', 'Inter', ui-sans-serif, system-ui, sans-serif !important;
        }
        /* Always use Inter for form controls and monospace elements */
        [dir=rtl] input, [dir=rtl] select, [dir=rtl] textarea,
        [dir=rtl] .font-mono, [dir=rtl] code, [dir=rtl] pre {
            font-family: 'Inter', ui-sans-serif, system-ui, sans-serif !important;
        }
        [dir=rtl] aside { left: auto !important; right: 0 !important; }
        [dir=rtl] .main-content { margin-left: 0 !important; margin-right: 16rem !important; }
        [dir=rtl] #toast-container { left: 1rem; right: auto; }
    </style>
    @endif
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-100 min-h-screen flex">

    {{-- PWA: Offline banner --}}
    <div id="offline-bar"
         class="hidden fixed top-0 left-0 right-0 z-50 bg-amber-500 text-white text-xs font-semibold text-center py-1.5 flex items-center justify-center gap-2">
        <span>📴</span> {{ __("You're offline — changes will sync when you reconnect") }}
    </div>

    {{-- PWA: Toast container --}}
    <div id="toast-container"
         class="fixed bottom-4 right-4 z-50 flex flex-col gap-2 items-end pointer-events-none">
    </div>

    {{-- Sidebar --}}
    <aside class="w-64 bg-slate-900 text-white flex flex-col fixed top-0 {{ $isRtl ? 'right-0' : 'left-0' }} bottom-0 z-20">
        <div class="shrink-0 px-6 py-5 border-b border-slate-700">
            <h1 class="text-lg font-bold tracking-wide">✂️ Suit Tailor</h1>
            <p class="text-xs text-slate-400 mt-0.5">{{ __('Management System') }}</p>
        </div>

        <nav class="flex-1 min-h-0 px-3 py-4 space-y-0.5 overflow-y-auto
                    [&::-webkit-scrollbar]:w-1.5
                    [&::-webkit-scrollbar-track]:bg-slate-800
                    [&::-webkit-scrollbar-thumb]:bg-slate-600
                    [&::-webkit-scrollbar-thumb]:rounded-full
                    [&::-webkit-scrollbar-thumb:hover]:bg-slate-500">
            <a href="{{ route('dashboard') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('dashboard') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800' }}">
                <span>📊</span> {{ __('Dashboard') }}
            </a>

            @if(auth()->user()->isAdmin() || auth()->user()->isBranchManager())
            <div class="pt-3 pb-1 px-3">
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">{{ __('Customers') }}</p>
            </div>
            <a href="{{ route('customers.index') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('customers.*') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800' }}">
                <span>👤</span> {{ __('Customers') }}
            </a>

            <div class="pt-3 pb-1 px-3">
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">{{ __('Orders') }}</p>
            </div>
            <a href="{{ route('pos.index') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('pos.*') ? 'bg-green-600 text-white' : 'text-green-400 hover:bg-slate-800' }}">
                <span>🛒</span> {{ __('New Order (POS)') }}
            </a>
            <a href="{{ route('orders.index') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('orders.*') && !request()->routeIs('pos.*') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800' }}">
                <span>🧾</span> {{ __('Orders') }}
            </a>
            <a href="{{ route('suits.index') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('suits.*') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800' }}">
                <span>👔</span> {{ __('Suits') }}
            </a>

            <div class="pt-3 pb-1 px-3">
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">{{ __('People') }}</p>
            </div>
            <a href="{{ route('workers.index') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('workers.*') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800' }}">
                <span>👨‍🔧</span> {{ __('Workers') }}
            </a>

            <div class="pt-3 pb-1 px-3">
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">{{ __('Inventory') }}</p>
            </div>
            <a href="{{ route('fabrics.index') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('fabrics.*') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800' }}">
                <span>🧶</span> {{ __('Fabric Stock') }}
            </a>
            <a href="{{ route('fabric-sales.create') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('fabric-sales.*') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800' }}">
                <span>🧾</span> {{ __('Fabric Sale') }}
            </a>

            <div class="pt-3 pb-1 px-3">
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">{{ __('Finance') }}</p>
            </div>
            <a href="{{ route('expenses.index') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('expenses.*') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800' }}">
                <span>💸</span> {{ __('Expenses') }}
            </a>

            <div class="pt-3 pb-1 px-3">
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">{{ __('Reports') }}</p>
            </div>
            <a href="{{ route('reports.daily') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('reports.daily') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800' }}">
                <span>📅</span> {{ __('Daily Orders') }}
            </a>
            <a href="{{ route('reports.pending') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('reports.pending') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800' }}">
                <span>⏳</span> {{ __('Pending Orders') }}
            </a>
            <a href="{{ route('reports.delivered') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('reports.delivered') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800' }}">
                <span>✅</span> {{ __('Delivered') }}
            </a>
            <a href="{{ route('reports.salary') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('reports.salary') && !request()->routeIs('reports.salary-report*') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800' }}">
                <span>💼</span> {{ __('Salary Report') }}
            </a>
            <a href="{{ route('reports.salary-report') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('reports.salary-report*') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800' }}">
                <span>💰</span> {{ __('Salary Disbursement') }}
            </a>
            <a href="{{ route('reports.pending-balances') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('reports.pending-balances') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800' }}">
                <span>🔴</span> {{ __('Pending Balances') }}
            </a>
            <a href="{{ route('reports.payments') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('reports.payments') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800' }}">
                <span>💳</span> {{ __('Payments') }}
            </a>
            <a href="{{ route('payments.create') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('payments.create') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800' }}">
                <span>➕</span> {{ __('Add Payment') }}
            </a>
            <a href="{{ route('reports.workers') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('reports.workers') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800' }}">
                <span>👷</span> {{ __('Workers Report') }}
            </a>
            <a href="{{ route('reports.fabric-profit') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('reports.fabric-profit') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800' }}">
                <span>📊</span> {{ __('Fabric Profit') }}
            </a>

            @if(auth()->user()->isAdmin())
            <div class="pt-3 pb-1 px-3">
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">{{ __('Admin') }}</p>
            </div>
            <a href="{{ route('branches.index') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('branches.*') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800' }}">
                <span>🏢</span> {{ __('Branches') }}
            </a>
            <a href="{{ route('stitch-types.index') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('stitch-types.*') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800' }}">
                <span>🧵</span> {{ __('Stitch Types') }}
            </a>
            <a href="{{ route('suit-types.index') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('suit-types.*') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800' }}">
                <span>👔</span> {{ __('Suit Types') }}
            </a>
            <a href="{{ route('extra-types.index') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('extra-types.*') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800' }}">
                <span>➕</span> Extra Types
            </a>
            <a href="{{ route('users.index') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('users.*') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800' }}">
                <span>👥</span> {{ __('User Management') }}
            </a>
            <a href="{{ route('settings.index') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('settings.index') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800' }}">
                <span>⚙️</span> {{ __('Settings') }}
            </a>
            <a href="{{ route('settings.predefined-notes') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('settings.predefined-notes') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800' }}">
                <span>📝</span> {{ __('Predefined Notes') }}
            </a>
            <a href="{{ route('translations.index') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('translations.*') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800' }}">
                <span>🌐</span> {{ __('Translations') }}
            </a>
            @endif

            @else
            <a href="{{ route('worker.suits') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('worker.suits') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800' }}">
                <span>👔</span> {{ __('My Suits') }}
            </a>
            @endif
        </nav>

        <div class="shrink-0 px-3 py-4 border-t border-slate-700 bg-slate-900">
            <div class="text-xs text-slate-400 mb-2 px-3">{{ auth()->user()->name }}</div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                    class="flex items-center gap-3 w-full px-3 py-2 rounded-lg text-sm text-slate-300 hover:bg-slate-800">
                    <span>🚪</span> {{ __('Logout') }}
                </button>
            </form>
        </div>
    </aside>

    {{-- Main content --}}
    <div class="main-content flex-1 {{ $isRtl ? 'mr-64' : 'ml-64' }} flex flex-col min-h-screen">
        <header class="bg-white border-b border-slate-200 px-6 py-3 flex items-center justify-between sticky top-0 z-10 shrink-0">
            <h2 class="text-base font-semibold text-slate-700">@yield('page-title', __('Dashboard'))</h2>
            <div class="flex items-center gap-3 flex-wrap">
                @if(auth()->user()->isAdmin())
                <form method="GET" action="{{ route('search') }}" class="flex">
                    <input type="text" name="q" value="{{ request('q') }}"
                        placeholder="{{ __('Search customer, mobile, code…') }}"
                        class="text-sm border border-slate-300 {{ $isRtl ? 'rounded-r-lg border-l-0' : 'rounded-l-lg' }} px-3 py-1.5 focus:outline-none focus:ring-2 focus:ring-blue-500 w-52">
                    <button type="submit"
                        class="bg-blue-600 text-white text-sm px-3 py-1.5 {{ $isRtl ? 'rounded-l-lg' : 'rounded-r-lg' }} hover:bg-blue-700">🔍</button>
                </form>
                @endif
                @if(auth()->user()->isBranchManager() && auth()->user()->branch)
                <span class="text-xs text-blue-700 bg-blue-50 border border-blue-200 px-2 py-1 rounded-full font-medium">
                    {{ auth()->user()->branch->name }}
                </span>
                @endif
                <span class="text-xs text-slate-500 bg-slate-100 px-2 py-1 rounded-full">
                    {{ ucfirst(str_replace('_', ' ', auth()->user()->role)) }}
                </span>

                {{-- Language switcher --}}
                <div class="flex items-center border border-slate-200 rounded-full overflow-hidden">
                    <a href="{{ route('lang.switch', 'en') }}"
                       class="text-xs px-2.5 py-1 font-semibold transition {{ app()->getLocale() === 'en' ? 'bg-blue-600 text-white' : 'text-slate-500 hover:bg-slate-100' }}">
                        EN
                    </a>
                    <a href="{{ route('lang.switch', 'ur') }}"
                       class="text-xs px-2.5 py-1 font-semibold transition {{ app()->getLocale() === 'ur' ? 'bg-blue-600 text-white' : 'text-slate-500 hover:bg-slate-100' }}"
                       style="font-family:'Noto Nastaliq Urdu',serif">
                        اردو
                    </a>
                </div>

                {{-- PWA buttons --}}
                <button id="sync-now-btn" title="Sync"
                    class="relative text-xs text-slate-500 hover:text-slate-700 bg-slate-100 hover:bg-slate-200 px-2 py-1 rounded-full transition">
                    🔄 <span id="sync-timestamp" class="hidden sm:inline"></span>
                    <span id="sync-badge" class="hidden absolute -top-1 -right-1 bg-amber-500 text-white text-[10px] font-bold rounded-full w-4 h-4 flex items-center justify-center"></span>
                </button>
                <button id="pwa-install-btn" class="hidden text-xs bg-blue-600 hover:bg-blue-700 text-white font-semibold px-3 py-1 rounded-full transition">
                    ⬇ {{ __('Install App') }}
                </button>
            </div>
        </header>

        <div class="px-6 pt-4">
            @if(session('success'))
                <div class="mb-4 p-3 bg-green-50 border border-green-200 rounded-lg text-sm text-green-700">
                    ✅ {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg text-sm text-red-700">
                    ❌ {{ session('error') }}
                </div>
            @endif
        </div>

        <main class="flex-1 px-6 pb-8">
            @yield('content')
        </main>
    </div>

    {{-- Quick Pay Modal (global) --}}
    <div id="qp-overlay" onclick="closePayModal()"
         style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.55);z-index:9999;align-items:center;justify-content:center;">
        <div onclick="event.stopPropagation()"
             style="background:#fff;border-radius:14px;padding:28px 32px;width:100%;max-width:480px;margin:16px;box-shadow:0 25px 60px rgba(0,0,0,0.3);">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;">
                <h3 id="qp-title" style="font-size:16px;font-weight:700;color:#1e293b">{{ __('Record Payment') }}</h3>
                <button onclick="closePayModal()" style="font-size:18px;color:#94a3b8;background:none;border:none;cursor:pointer;">&times;</button>
            </div>
            <p id="qp-balance" style="font-size:12px;color:#64748b;margin-bottom:16px;"></p>
            <form id="qp-form" method="POST">
                @csrf
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:12px;">
                    <div>
                        <label style="display:block;font-size:11px;font-weight:600;color:#475569;margin-bottom:4px;">{{ __('Amount') }} (Rs) *</label>
                        <input type="number" id="qp-amount" name="amount" min="1" step="0.01" required
                            style="width:100%;border:1px solid #cbd5e1;border-radius:8px;padding:7px 10px;font-size:13px;outline:none;">
                    </div>
                    <div>
                        <label style="display:block;font-size:11px;font-weight:600;color:#475569;margin-bottom:4px;">{{ __('Method') }} *</label>
                        <select name="method" style="width:100%;border:1px solid #cbd5e1;border-radius:8px;padding:7px 10px;font-size:13px;outline:none;">
                            <option value="cash">{{ __('Cash') }}</option>
                            <option value="bank_transfer">{{ __('Bank Transfer') }}</option>
                            <option value="cheque">{{ __('Cheque') }}</option>
                            <option value="online">{{ __('Online') }}</option>
                        </select>
                    </div>
                    <div>
                        <label style="display:block;font-size:11px;font-weight:600;color:#475569;margin-bottom:4px;">{{ __('Date') }} *</label>
                        <input type="date" name="payment_date" id="qp-date" required
                            style="width:100%;border:1px solid #cbd5e1;border-radius:8px;padding:7px 10px;font-size:13px;outline:none;">
                    </div>
                    <div>
                        <label style="display:block;font-size:11px;font-weight:600;color:#475569;margin-bottom:4px;">{{ __('Reference') }}</label>
                        <input type="text" name="reference"
                            style="width:100%;border:1px solid #cbd5e1;border-radius:8px;padding:7px 10px;font-size:13px;outline:none;">
                    </div>
                </div>
                <div style="margin-bottom:16px;">
                    <label style="display:block;font-size:11px;font-weight:600;color:#475569;margin-bottom:4px;">{{ __('Note') }}</label>
                    <input type="text" name="note"
                        style="width:100%;border:1px solid #cbd5e1;border-radius:8px;padding:7px 10px;font-size:13px;outline:none;">
                </div>
                <div style="display:flex;gap:10px;">
                    <button type="submit"
                        style="flex:1;background:#16a34a;color:#fff;border:none;border-radius:8px;padding:9px 0;font-size:13px;font-weight:600;cursor:pointer;">
                        ✓ {{ __('Record Payment') }}
                    </button>
                    <button type="button" onclick="closePayModal()"
                        style="background:#f1f5f9;color:#475569;border:none;border-radius:8px;padding:9px 16px;font-size:13px;font-weight:600;cursor:pointer;">
                        {{ __('Cancel') }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    @stack('scripts')
    <script>
        function openPayModal(orderId, orderNum, balance) {
            document.getElementById('qp-form').action = '/orders/' + orderId + '/payments';
            document.getElementById('qp-title').textContent = '{{ __("Record Payment") }} — ' + orderNum;
            document.getElementById('qp-balance').textContent = 'Rs ' + Number(balance).toLocaleString();
            var amtInput = document.getElementById('qp-amount');
            amtInput.value = balance > 0 ? balance : '';
            amtInput.max   = balance;
            document.getElementById('qp-date').value = new Date().toISOString().substring(0, 10);
            document.getElementById('qp-overlay').style.display = 'flex';
        }
        function closePayModal() {
            document.getElementById('qp-overlay').style.display = 'none';
        }
        function selectPredefinedNote(selectEl) {
            if (!selectEl.value) return;
            const container = selectEl.closest('.notes-container');
            if (container) {
                const field = container.querySelector('textarea, input[type=text]');
                if (field) {
                    if (selectEl.value === 'custom') {
                        field.value = '';
                    } else {
                        field.value = field.value ? (field.value + ' · ' + selectEl.value) : selectEl.value;
                    }
                    field.dispatchEvent(new Event('input', { bubbles: true }));
                    field.dispatchEvent(new Event('change', { bubbles: true }));
                    field.focus();
                }
            }
            selectEl.value = '';
        }
    </script>
</body>
</html>
