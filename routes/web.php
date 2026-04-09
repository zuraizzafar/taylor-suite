<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MeasurementController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ScanController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\SuitController;
use App\Http\Controllers\WorkerController;
use App\Http\Controllers\WorkerPortalController;
use Illuminate\Support\Facades\Route;

// ─── Public: QR scan (no auth required) ───────────────────────────────────────
Route::get('/scan/{code}', [ScanController::class, 'show'])->name('scan.show');

// ─── Auth ──────────────────────────────────────────────────────────────────────
Route::get('/login', [AuthController::class, 'showLogin'])->name('login')->middleware('guest');
Route::post('/login', [AuthController::class, 'login'])->middleware('guest');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ─── Authenticated area ────────────────────────────────────────────────────────
Route::middleware('auth')->group(function () {

    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Worker portal (worker role only)
    Route::get('/worker/suits', [WorkerPortalController::class, 'suits'])->name('worker.suits');

    // Suit status update (both admin + worker can do this)
    Route::patch('/suits/{suit}/status', [SuitController::class, 'updateStatus'])->name('suits.status');

    // ── Admin-only routes ──────────────────────────────────────────────────────
    Route::middleware('role:admin')->group(function () {

        // Customers
        Route::resource('customers', CustomerController::class);

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

        // Orders
        Route::resource('orders', OrderController::class);
        Route::get('/orders/{order}/invoice', [OrderController::class, 'invoice'])->name('orders.invoice');

        // Suits
        Route::resource('suits', SuitController::class)->except(['destroy']);
        Route::delete('/suits/{suit}', [SuitController::class, 'destroy'])->name('suits.destroy');
        Route::patch('/suits/{suit}/assign-worker', [SuitController::class, 'assignWorker'])->name('suits.assign-worker');
        Route::get('/suits/{suit}/tag', [SuitController::class, 'tag'])->name('suits.tag');

        // Search
        Route::get('/search', [SearchController::class, 'index'])->name('search');

        // Reports
        Route::get('/reports/daily', [ReportController::class, 'daily'])->name('reports.daily');
        Route::get('/reports/pending', [ReportController::class, 'pending'])->name('reports.pending');
        Route::get('/reports/delivered', [ReportController::class, 'delivered'])->name('reports.delivered');
    });
});

