@extends('layouts.app')
@section('title', __('Reports Dashboard'))
@section('page-title', __('Reports Dashboard'))

@section('content')
<div class="max-w-6xl mx-auto space-y-6 pt-2">
    <!-- Header Summary Card with premium dark/indigo gradient -->
    <div class="bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 rounded-2xl p-6 text-white shadow-xl relative overflow-hidden">
        <div class="absolute right-0 top-0 opacity-10 transform translate-x-12 -translate-y-12">
            <span class="text-[180px] leading-none">📊</span>
        </div>
        <div class="relative z-10 space-y-2">
            <h2 class="text-xl font-bold tracking-tight md:text-2xl">{{ __('Tailor Suite Analytics & Reports') }}</h2>
            <p class="text-slate-300 text-sm max-w-xl">
                {{ __('Select a report type below to audit daily business volume, pending tasks, payouts, receivables, fabric profitability, and more. Export your data instantly to Excel/CSV or print.') }}
            </p>
        </div>
    </div>

    <!-- Reports Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @php
            $reportsList = [
                [
                    'title' => __('Daily Orders'),
                    'desc' => __('Audit all customer orders, advance payments, and metrics received on a specific date.'),
                    'icon' => '📅',
                    'route' => 'reports.daily',
                    'report_key' => 'daily',
                    'color' => 'from-blue-500 to-indigo-600',
                    'badge' => __('Order Log')
                ],
                [
                    'title' => __('Pending Orders'),
                    'desc' => __('Monitor suits still in cutting, stitching, or ready state. Filter by specific progress stage.'),
                    'icon' => '⏳',
                    'route' => 'reports.pending',
                    'report_key' => 'pending',
                    'color' => 'from-amber-500 to-orange-600',
                    'badge' => __('Work-in-Progress')
                ],
                [
                    'title' => __('Delivered Suits'),
                    'desc' => __('Summary of items successfully delivered in a date range, with counts and calculated gross worth.'),
                    'icon' => '✅',
                    'route' => 'reports.delivered',
                    'report_key' => 'delivered',
                    'color' => 'from-emerald-500 to-teal-600',
                    'badge' => __('Closed orders')
                ],
                [
                    'title' => __('Salary Report (Summary)'),
                    'desc' => __('Check total stitched garments count and estimated earnings per worker within custom periods.'),
                    'icon' => '💼',
                    'route' => 'reports.salary',
                    'report_key' => 'salary',
                    'color' => 'from-purple-500 to-violet-600',
                    'badge' => __('Earnings Summary')
                ],
                [
                    'title' => __('Salary Disbursement (Detailed)'),
                    'desc' => __('Comprehensive breakdown of suits, payouts, disbursements, and individual current balances.'),
                    'icon' => '💰',
                    'route' => 'reports.salary-report',
                    'report_key' => 'salary', // maps to salary in csv
                    'color' => 'from-cyan-500 to-blue-600',
                    'badge' => __('Financial Ledger')
                ],
                [
                    'title' => __('Pending Balances'),
                    'desc' => __('List of outstanding receivables grouped by customer, sorted by highest outstanding due.'),
                    'icon' => '🔴',
                    'route' => 'reports.pending-balances',
                    'report_key' => 'pending-balances',
                    'color' => 'from-rose-500 to-red-600',
                    'badge' => __('Receivables')
                ],
                [
                    'title' => __('Payments Received'),
                    'desc' => __('Audit history of transaction logs, payments, and payment methods (Cash, Bank, EasyPaisa).'),
                    'icon' => '💳',
                    'route' => 'reports.payments',
                    'report_key' => 'payments',
                    'color' => 'from-teal-500 to-emerald-600',
                    'badge' => __('Cashflow')
                ],
                [
                    'title' => __('Workers Summary'),
                    'desc' => __('Evaluate worker performance, suits stitched, earnings, paid salary, and pending worker payables.'),
                    'icon' => '👷',
                    'route' => 'reports.workers',
                    'report_key' => 'workers',
                    'color' => 'from-slate-600 to-slate-800',
                    'badge' => __('HR / Performance')
                ],
                [
                    'title' => __('Fabric Sales & Profit'),
                    'desc' => __('Calculate fabric retail metrics: meters sold, roll logs, base costs, and generated net profits.'),
                    'icon' => '📊',
                    'route' => 'reports.fabric-profit',
                    'report_key' => 'fabric-profit',
                    'color' => 'from-violet-500 to-fuchsia-600',
                    'badge' => __('Inventory Profit')
                ],
            ];
        @endphp

        @foreach($reportsList as $item)
        <div class="group bg-white rounded-2xl shadow-sm border border-slate-100 p-6 flex flex-col justify-between hover:shadow-md hover:border-slate-200 transition-all duration-300 transform hover:-translate-y-1">
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br {{ $item['color'] }} flex items-center justify-center text-white text-2xl shadow-sm group-hover:scale-110 transition-transform duration-300">
                        {{ $item['icon'] }}
                    </div>
                    <span class="text-[10px] uppercase font-bold tracking-widest text-slate-400 bg-slate-50 px-2.5 py-1 rounded-full border border-slate-100">{{ $item['badge'] }}</span>
                </div>
                <div class="space-y-1">
                    <h3 class="font-bold text-slate-800 text-lg group-hover:text-blue-600 transition-colors duration-300">{{ $item['title'] }}</h3>
                    <p class="text-slate-500 text-xs leading-relaxed">{{ $item['desc'] }}</p>
                </div>
            </div>

            <div class="pt-5 mt-5 border-t border-slate-50 flex items-center justify-between gap-2">
                <a href="{{ route($item['route']) }}" 
                   class="flex-1 text-center bg-slate-50 hover:bg-blue-50 text-slate-700 hover:text-blue-700 font-semibold text-xs py-2 px-3 rounded-lg border border-slate-200/60 hover:border-blue-200 transition-all duration-250">
                    👁️ {{ __('View') }}
                </a>
                <a href="{{ route('reports.export-csv', ['report' => $item['report_key']]) }}" 
                   class="flex-1 text-center bg-emerald-50 hover:bg-emerald-600 text-emerald-700 hover:text-white font-semibold text-xs py-2 px-3 rounded-lg border border-emerald-100 hover:border-emerald-600 transition-all duration-250">
                    📥 {{ __('Excel') }}
                </a>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection
