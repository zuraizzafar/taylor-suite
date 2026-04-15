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
    <aside class="w-64 bg-slate-900 text-white flex flex-col min-h-screen fixed top-0 left-0 z-20">
        <div class="px-6 py-5 border-b border-slate-700">
            <h1 class="text-lg font-bold tracking-wide">✂️ Suit Tailor</h1>
            <p class="text-xs text-slate-400 mt-0.5">Management System</p>
        </div>

        <nav class="flex-1 px-3 py-4 space-y-0.5 overflow-y-auto">
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

            @if(auth()->user()->isAdmin())
            <div class="pt-3 pb-1 px-3">
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Admin</p>
            </div>
            <a href="{{ route('branches.index') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('branches.*') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800' }}">
                <span>🏢</span> Branches
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

        <div class="px-3 py-4 border-t border-slate-700">
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
        <header class="bg-white border-b border-slate-200 px-6 py-3 flex items-center justify-between sticky top-0 z-10">
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

    @stack('scripts')
</body>
</html>
