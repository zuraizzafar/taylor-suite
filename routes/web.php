<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\KillSwitchController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\SyncController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExtraTypeController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\FabricController;
use App\Http\Controllers\FabricSaleController;
use App\Http\Controllers\MeasurementController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\QuotationController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ScanController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\StitchTypeController;
use App\Http\Controllers\SuitController;
use App\Http\Controllers\SuitTypeController;
use App\Http\Controllers\TranslationController;
use App\Http\Controllers\WorkerController;
use App\Http\Controllers\WorkerSalaryController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WorkerPortalController;
use Illuminate\Support\Facades\Route;

// ─── Language switcher (public, no auth) ─────────────────────────────────────
Route::get('/lang/{locale}', function (string $locale) {
    if (in_array($locale, ['en', 'ur'])) {
        session(['locale' => $locale]);
    }
    return redirect()->back();
})->name('lang.switch');

// ─── Public: QR scan (no auth required) ───────────────────────────────────────
Route::get('/scan/{code}', [ScanController::class, 'show'])->name('scan.show');
Route::get('/track', [ScanController::class, 'tracking'])->name('tracking.index');
Route::get('/track/{tracking}', [ScanController::class, 'trackingShow'])->name('tracking.show');

// ─── Auth ──────────────────────────────────────────────────────────────────────
Route::get('/login', [AuthController::class, 'showLogin'])->name('login')->middleware('guest');
Route::post('/login', [AuthController::class, 'login'])->middleware('guest');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ─── PWA sync (requires auth, JSON only) ──────────────────────────────────────
Route::middleware('auth')->group(function () {
    Route::get('/sync/pull', [SyncController::class, 'pull'])->name('sync.pull');
    Route::post('/sync/push', [SyncController::class, 'push'])->name('sync.push');
});

// ─── Authenticated area ────────────────────────────────────────────────────────
Route::middleware('auth')->group(function () {

    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Worker portal (worker role only)
    Route::get('/worker/suits', [WorkerPortalController::class, 'suits'])->name('worker.suits');

    // Suit status update (admin + branch_manager + worker)
    Route::patch('/suits/{suit}/status', [SuitController::class, 'updateStatus'])->name('suits.status');

    // ── Admin + Branch Manager routes ──────────────────────────────────────────
    Route::middleware('role:admin,branch_manager')->group(function () {

        // Customers
        Route::resource('customers', CustomerController::class);
        Route::get('/customers/{customer}/tags/pending', [CustomerController::class, 'pendingTags'])->name('customers.tags.pending');
        Route::get('/customers/{customer}/statement/pdf', [CustomerController::class, 'statementPdf'])->name('customers.statement-pdf');

        // Measurements (nested under customer)
        Route::get('/customers/{customer}/measurements/create', [MeasurementController::class, 'create'])
            ->name('measurements.create');
        Route::post('/customers/{customer}/measurements', [MeasurementController::class, 'store'])
            ->name('measurements.store');
        Route::get('/customers/{customer}/measurements/{measurement}/edit', [MeasurementController::class, 'edit'])
            ->name('measurements.edit');
        Route::put('/customers/{customer}/measurements/{measurement}', [MeasurementController::class, 'update'])
            ->name('measurements.update');

        // Workers
        Route::resource('workers', WorkerController::class)->except(['show']);

        // Quotations (draft invoices — convertible into a real Order)
        Route::resource('quotations', QuotationController::class);
        Route::get('/quotations/{quotation}/pdf', [QuotationController::class, 'pdf'])->name('quotations.pdf');
        Route::post('/quotations/{quotation}/convert', [QuotationController::class, 'convert'])->name('quotations.convert');

        // Orders
        Route::resource('orders', OrderController::class);
        Route::get('/orders/{order}/invoice', [OrderController::class, 'invoice'])->name('orders.invoice');
        Route::get('/orders/{order}/tags', [OrderController::class, 'tags'])->name('orders.tags');

        // Payments (nested under orders + standalone create/search/edit/update/delete)
        Route::get('/payments/create', [PaymentController::class, 'create'])->name('payments.create');
        Route::get('/payments/search', [PaymentController::class, 'search'])->name('payments.search');
        Route::post('/orders/{order}/payments', [PaymentController::class, 'store'])->name('orders.payments.store');
        Route::get('/payments/{payment}/edit', [PaymentController::class, 'edit'])->name('payments.edit');
        Route::put('/payments/{payment}', [PaymentController::class, 'update'])->name('payments.update');
        Route::delete('/payments/{payment}', [PaymentController::class, 'destroy'])->name('payments.destroy');

        // Suits
        Route::resource('suits', SuitController::class)->except(['destroy']);
        Route::delete('/suits/{suit}', [SuitController::class, 'destroy'])->name('suits.destroy');
        Route::patch('/suits/{suit}/assign-worker', [SuitController::class, 'assignWorker'])->name('suits.assign-worker');
        Route::get('/suits/{suit}/tag', [SuitController::class, 'tag'])->name('suits.tag');

        // Search
        Route::get('/search', [SearchController::class, 'index'])->name('search');

        // Reports
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/daily', [ReportController::class, 'daily'])->name('reports.daily');
        Route::get('/reports/daily/pdf', [ReportController::class, 'dailyPdf'])->name('reports.daily-pdf');
        Route::get('/reports/pending', [ReportController::class, 'pending'])->name('reports.pending');
        Route::get('/reports/pending/pdf', [ReportController::class, 'pendingPdf'])->name('reports.pending-pdf');
        Route::get('/reports/delivered', [ReportController::class, 'delivered'])->name('reports.delivered');
        Route::get('/reports/delivered/pdf', [ReportController::class, 'deliveredPdf'])->name('reports.delivered-pdf');
        Route::get('/reports/salary', [ReportController::class, 'salary'])->name('reports.salary');
        Route::get('/reports/salary/pdf', [ReportController::class, 'salaryPdf'])->name('reports.salary-pdf');
        Route::get('/reports/salary-report', [ReportController::class, 'salaryReport'])->name('reports.salary-report');
        Route::get('/reports/salary-report/pdf', [ReportController::class, 'salaryReportPdf'])->name('reports.salary-report-pdf');
        Route::get('/reports/pending-balances', [ReportController::class, 'pendingBalances'])->name('reports.pending-balances');
        Route::get('/reports/pending-balances/pdf', [ReportController::class, 'pendingBalancesPdf'])->name('reports.pending-balances-pdf');
        Route::get('/reports/payments', [ReportController::class, 'payments'])->name('reports.payments');
        Route::get('/reports/payments/pdf', [ReportController::class, 'paymentsPdf'])->name('reports.payments-pdf');
        Route::get('/reports/workers', [ReportController::class, 'workers'])->name('reports.workers');
        Route::get('/reports/workers/pdf', [ReportController::class, 'workersPdf'])->name('reports.workers-pdf');
        Route::get('/reports/{report}/export-csv', [ReportController::class, 'exportCsv'])->name('reports.export-csv');

        // POS — quick order creation
        Route::get('/pos', [PosController::class, 'index'])->name('pos.index');
        Route::post('/pos', [PosController::class, 'store'])->name('pos.store');
        Route::get('/pos/customers/search', [PosController::class, 'searchCustomers'])->name('pos.customers.search');

        // Worker Report & Salary Payments
        Route::get('/workers/{worker}/report', [WorkerController::class, 'report'])->name('workers.report');
        Route::post('/workers/{worker}/salary-payments', [WorkerSalaryController::class, 'store'])->name('workers.salary-payments.store');
        Route::delete('/salary-payments/{salaryPayment}', [WorkerSalaryController::class, 'destroy'])->name('workers.salary-payments.destroy');

        // Expenses
        Route::resource('expenses', ExpenseController::class);

        // Fabrics (stock module)
        Route::resource('fabrics', FabricController::class)->except(['show']);
        Route::patch('/fabrics/{fabric}/add-meter', [FabricController::class, 'addMeter'])->name('fabrics.add-meter');
        Route::patch('/fabrics/{fabric}/reduce-meter', [FabricController::class, 'reduceMeter'])->name('fabrics.reduce-meter');
        Route::get('/fabrics/{fabric}/history', [FabricController::class, 'history'])->name('fabrics.history');
        Route::get('/fabrics/{fabric}/sticker', [FabricController::class, 'sticker'])->name('fabrics.sticker');
        Route::get('/fabrics-lookup', [FabricController::class, 'lookup'])->name('fabrics.lookup');

        // Fabric Sales (walk-in retail sale)
        Route::get('/fabric-sales/create', [FabricSaleController::class, 'create'])->name('fabric-sales.create');
        Route::post('/fabric-sales', [FabricSaleController::class, 'store'])->name('fabric-sales.store');
        Route::get('/fabric-sales/{fabricSale}/invoice', [FabricSaleController::class, 'invoice'])->name('fabric-sales.invoice');

        Route::get('/reports/fabric-profit', [ReportController::class, 'fabricProfit'])->name('reports.fabric-profit');
        Route::get('/reports/fabric-profit/pdf', [ReportController::class, 'fabricProfitPdf'])->name('reports.fabric-profit-pdf');
    });

    // ── Admin-only routes ──────────────────────────────────────────────────────
    Route::middleware('role:admin')->group(function () {
        Route::resource('branches', BranchController::class);
        Route::resource('stitch-types', StitchTypeController::class)->only(['index', 'store', 'update', 'destroy']);
        Route::resource('suit-types', SuitTypeController::class)->only(['index', 'store', 'update', 'destroy']);
        Route::resource('extra-types', ExtraTypeController::class)->only(['index', 'store', 'update', 'destroy']);
        Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
        Route::put('/settings', [SettingController::class, 'update'])->name('settings.update');
        Route::get('/settings/predefined-notes', [SettingController::class, 'predefinedNotes'])->name('settings.predefined-notes');
        Route::put('/settings/predefined-notes', [SettingController::class, 'updatePredefinedNotes'])->name('settings.predefined-notes.update');

        // User management (branch managers + workers)
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::get('/users/{user}/password', [UserController::class, 'editPassword'])->name('users.password');
        Route::put('/users/{user}/password', [UserController::class, 'updatePassword'])->name('users.password.update');

        // Translation management
        Route::get('/translations', [TranslationController::class, 'index'])->name('translations.index');
        Route::post('/translations', [TranslationController::class, 'update'])->name('translations.update');
        Route::delete('/translations', [TranslationController::class, 'destroy'])->name('translations.destroy');
    });
});

