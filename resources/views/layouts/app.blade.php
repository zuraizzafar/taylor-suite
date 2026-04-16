<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'The Suit Tailor')</title>
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">
    @vite(['resources/css/app.css'])
</head>
<body class="bg-slate-100 min-h-screen flex">

    {{-- Sidebar --}}
    <aside class="w-64 bg-slate-900 text-white flex flex-col fixed top-0 left-0 bottom-0 z-20">
        {{-- Brand – fixed at top --}}
        <div class="shrink-0 px-6 py-5 border-b border-slate-700">
            <h1 class="text-lg font-bold tracking-wide">✂️ Suit Tailor</h1>
            <p class="text-xs text-slate-400 mt-0.5">Management System</p>
        </div>

        {{-- Scrollable nav –– fills remaining height --}}
        <nav class="flex-1 min-h-0 px-3 py-4 space-y-0.5 overflow-y-auto
                    [&::-webkit-scrollbar]:w-1.5
                    [&::-webkit-scrollbar-track]:bg-slate-800
                    [&::-webkit-scrollbar-thumb]:bg-slate-600
                    [&::-webkit-scrollbar-thumb]:rounded-full
                    [&::-webkit-scrollbar-thumb:hover]:bg-slate-500">
            <a href="{{ route('dashboard') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('dashboard') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800' }}">
                <span>📊</span> Dashboard
            </a>

            @if(auth()->user()->isAdmin() || auth()->user()->isBranchManager())
            <div class="pt-3 pb-1 px-3">
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Customers</p>
            </div>
            <a href="{{ route('customers.index') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('customers.*') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800' }}">
                <span>👤</span> Customers
            </a>

            <div class="pt-3 pb-1 px-3">
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Orders</p>
            </div>
            <a href="{{ route('orders.index') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('orders.*') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800' }}">
                <span>🧾</span> Orders
            </a>
            <a href="{{ route('suits.index') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('suits.*') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800' }}">
                <span>👔</span> Suits
            </a>

            <div class="pt-3 pb-1 px-3">
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">People</p>
            </div>
            <a href="{{ route('workers.index') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('workers.*') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800' }}">
                <span>👨‍🔧</span> Workers
            </a>

            <div class="pt-3 pb-1 px-3">
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Finance</p>
            </div>
            <a href="{{ route('expenses.index') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('expenses.*') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800' }}">
                <span>💸</span> Expenses
            </a>

            <div class="pt-3 pb-1 px-3">
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Reports</p>
            </div>
            <a href="{{ route('reports.daily') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('reports.daily') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800' }}">
                <span>📅</span> Daily Orders
            </a>
            <a href="{{ route('reports.pending') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('reports.pending') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800' }}">
                <span>⏳</span> Pending Orders
            </a>
            <a href="{{ route('reports.delivered') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('reports.delivered') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800' }}">
                <span>✅</span> Delivered
            </a>
            <a href="{{ route('reports.salary') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('reports.salary') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800' }}">
                <span>💼</span> Salary Report
            </a>
            <a href="{{ route('reports.pending-balances') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('reports.pending-balances') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800' }}">
                <span>🔴</span> Pending Balances
            </a>
            <a href="{{ route('reports.payments') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('reports.payments') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800' }}">
                <span>💳</span> Payments
            </a>
            <a href="{{ route('payments.create') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('payments.create') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800' }}">
                <span>➕</span> Add Payment
            </a>

            @if(auth()->user()->isAdmin())
            <div class="pt-3 pb-1 px-3">
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Admin</p>
            </div>
            <a href="{{ route('branches.index') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('branches.*') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800' }}">
                <span>🏢</span> Branches
            </a>
            <a href="{{ route('users.index') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('users.*') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800' }}">
                <span>👥</span> User Management
            </a>
            <a href="{{ route('settings.index') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('settings.*') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800' }}">
                <span>⚙️</span> Settings
            </a>
            @endif

            @else
            {{-- Worker navigation --}}
            <a href="{{ route('worker.suits') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('worker.suits') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800' }}">
                <span>👔</span> My Suits
            </a>
            @endif
        </nav>

        {{-- Logout – pinned at bottom --}}
        <div class="shrink-0 px-3 py-4 border-t border-slate-700 bg-slate-900">
            <div class="text-xs text-slate-400 mb-2 px-3">{{ auth()->user()->name }}</div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                    class="flex items-center gap-3 w-full px-3 py-2 rounded-lg text-sm text-slate-300 hover:bg-slate-800">
                    <span>🚪</span> Logout
                </button>
            </form>
        </div>
    </aside>

    {{-- Main content --}}
    <div class="flex-1 ml-64 flex flex-col min-h-screen">
        {{-- Topbar --}}
        <header class="bg-white border-b border-slate-200 px-6 py-3 flex items-center justify-between sticky top-0 z-10 shrink-0">
            <h2 class="text-base font-semibold text-slate-700">@yield('page-title', 'Dashboard')</h2>
            <div class="flex items-center gap-4">
                @if(auth()->user()->isAdmin())
                <form method="GET" action="{{ route('search') }}" class="flex">
                    <input type="text" name="q" value="{{ request('q') }}"
                        placeholder="Search customer, mobile, code…"
                        class="text-sm border border-slate-300 rounded-l-lg px-3 py-1.5 focus:outline-none focus:ring-2 focus:ring-blue-500 w-56">
                    <button type="submit"
                        class="bg-blue-600 text-white text-sm px-3 py-1.5 rounded-r-lg hover:bg-blue-700">🔍</button>
                </form>
                @endif
                @if(auth()->user()->isBranchManager() && auth()->user()->branch)
                <span class="text-xs text-blue-700 bg-blue-50 border border-blue-200 px-2 py-1 rounded-full font-medium">
                    {{ auth()->user()->branch->name }}
                </span>
                @endif
                <span class="text-xs text-slate-500 bg-slate-100 px-2 py-1 rounded-full">
                    {{ ucfirst(auth()->user()->role) }}
                </span>
            </div>
        </header>

        {{-- Flash messages --}}
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

    {{-- ── Quick Pay Modal (global) ─────────────────────────────────────────── --}}
    <div id="qp-overlay"
         onclick="closePayModal()"
         style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.55);z-index:9999;align-items:center;justify-content:center;">
        <div onclick="event.stopPropagation()"
             style="background:#fff;border-radius:14px;padding:28px 32px;width:100%;max-width:480px;margin:16px;box-shadow:0 25px 60px rgba(0,0,0,0.3);">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;">
                <h3 id="qp-title" style="font-size:16px;font-weight:700;color:#1e293b">Record Payment</h3>
                <button onclick="closePayModal()" style="font-size:18px;color:#94a3b8;background:none;border:none;cursor:pointer;line-height:1">&times;</button>
            </div>
            <p id="qp-balance" style="font-size:12px;color:#64748b;margin-bottom:16px;"></p>
            <form id="qp-form" method="POST">
                @csrf
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:12px;">
                    <div>
                        <label style="display:block;font-size:11px;font-weight:600;color:#475569;margin-bottom:4px;">Amount (Rs) *</label>
                        <input type="number" id="qp-amount" name="amount" min="1" step="0.01" required
                            style="width:100%;border:1px solid #cbd5e1;border-radius:8px;padding:7px 10px;font-size:13px;outline:none;">
                    </div>
                    <div>
                        <label style="display:block;font-size:11px;font-weight:600;color:#475569;margin-bottom:4px;">Method *</label>
                        <select name="method" style="width:100%;border:1px solid #cbd5e1;border-radius:8px;padding:7px 10px;font-size:13px;outline:none;">
                            <option value="cash">Cash</option>
                            <option value="bank_transfer">Bank Transfer</option>
                            <option value="cheque">Cheque</option>
                            <option value="online">Online</option>
                        </select>
                    </div>
                    <div>
                        <label style="display:block;font-size:11px;font-weight:600;color:#475569;margin-bottom:4px;">Date *</label>
                        <input type="date" name="payment_date" id="qp-date" required
                            style="width:100%;border:1px solid #cbd5e1;border-radius:8px;padding:7px 10px;font-size:13px;outline:none;">
                    </div>
                    <div>
                        <label style="display:block;font-size:11px;font-weight:600;color:#475569;margin-bottom:4px;">Reference</label>
                        <input type="text" name="reference" placeholder="Txn ID, cheque no…"
                            style="width:100%;border:1px solid #cbd5e1;border-radius:8px;padding:7px 10px;font-size:13px;outline:none;">
                    </div>
                </div>
                <div style="margin-bottom:16px;">
                    <label style="display:block;font-size:11px;font-weight:600;color:#475569;margin-bottom:4px;">Note</label>
                    <input type="text" name="note" placeholder="Optional note"
                        style="width:100%;border:1px solid #cbd5e1;border-radius:8px;padding:7px 10px;font-size:13px;outline:none;">
                </div>
                <div style="display:flex;gap:10px;">
                    <button type="submit"
                        style="flex:1;background:#16a34a;color:#fff;border:none;border-radius:8px;padding:9px 0;font-size:13px;font-weight:600;cursor:pointer;">
                        ✓ Record Payment
                    </button>
                    <button type="button" onclick="closePayModal()"
                        style="background:#f1f5f9;color:#475569;border:none;border-radius:8px;padding:9px 16px;font-size:13px;font-weight:600;cursor:pointer;">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>

    @stack('scripts')
    <script>
        function openPayModal(orderId, orderNum, balance) {
            document.getElementById('qp-form').action = '/orders/' + orderId + '/payments';
            document.getElementById('qp-title').textContent = 'Payment — ' + orderNum;
            document.getElementById('qp-balance').textContent = 'Remaining balance: Rs ' + Number(balance).toLocaleString();
            var amtInput = document.getElementById('qp-amount');
            amtInput.value = balance > 0 ? balance : '';
            amtInput.max   = balance;
            document.getElementById('qp-date').value = new Date().toISOString().substring(0, 10);
            document.getElementById('qp-overlay').style.display = 'flex';
        }
        function closePayModal() {
            document.getElementById('qp-overlay').style.display = 'none';
        }
    </script>
</body>
</html>
