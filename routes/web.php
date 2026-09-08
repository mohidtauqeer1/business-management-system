<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\StockAdjustmentController;
use App\Http\Controllers\StockMovementController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\SupplierBalanceController;
use App\Http\Controllers\CustomerBalanceController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\SaleReturnController;
use App\Http\Controllers\PurchaseReturnController;

// ══════════════════════════════════════════════════════════════════
// Authentication (Public)
// ══════════════════════════════════════════════════════════════════

Route::get('/', fn() => redirect()->route('login'));

Route::get('/login', [AuthController::class, 'showLogin'])
    ->name('login');

Route::post('/login', [AuthController::class, 'login'])
    ->name('login.submit');

Route::post('/logout', [AuthController::class, 'logout'])
    ->name('logout');


// ══════════════════════════════════════════════════════════════════
// Authenticated Routes
// ══════════════════════════════════════════════════════════════════

Route::middleware('auth')->group(function () {

    // ── Dashboard ─────────────────────────────────────────────────
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    // ── Users (Admin only) ────────────────────────────────────────
    Route::middleware('role:admin')->group(function () {
        Route::resource('users', UserController::class);
    });

    // ── Catalog: Products & Categories (Admin + Manager + Staff) ──
    Route::middleware('role:admin,manager,staff')->group(function () {
        Route::resource('products',   ProductController::class);
        Route::resource('categories', CategoryController::class);
    });

    // ── Suppliers (Admin + Manager) ───────────────────────────────
    Route::middleware('role:admin,manager')->group(function () {
        Route::resource('suppliers', SupplierController::class);
    });

    // ── Customers (Admin + Manager + Cashier) ─────────────────────
    Route::middleware('role:admin,manager,cashier')->group(function () {
        Route::resource('customers', CustomerController::class);
    });

    // ── Inventory (Admin + Manager + Staff) ───────────────────────
    Route::middleware('role:admin,manager,staff')->group(function () {
        Route::get('/inventory', [InventoryController::class, 'index'])
            ->name('inventory.index');

        Route::get('/inventory/low-stock', [InventoryController::class, 'lowStock'])
            ->name('inventory.low-stock');

        Route::get('/inventory/movements', [StockMovementController::class, 'index'])
            ->name('inventory.movements');
    });

    // ── Stock Adjustment (Admin + Manager) ────────────────────────
    Route::middleware('role:admin,manager')->group(function () {
        Route::get('/inventory/adjustment', [StockAdjustmentController::class, 'create'])
            ->name('inventory.adjustment.create');

        Route::post('/inventory/adjustment', [StockAdjustmentController::class, 'store'])
            ->name('inventory.adjustment.store');
    });

    // ── Purchases (Admin + Manager) ───────────────────────────────
    Route::middleware('role:admin,manager')->group(function () {
        Route::get('/purchases',        [PurchaseController::class, 'index'])  ->name('purchases.index');
        Route::get('/purchases/create', [PurchaseController::class, 'create']) ->name('purchases.create');
        Route::post('/purchases',       [PurchaseController::class, 'store'])  ->name('purchases.store');
        Route::get('/purchases/{purchase}', [PurchaseController::class, 'show']) ->name('purchases.show');
    });

    // ── Sales (Admin + Manager + Cashier) ─────────────────────────
    Route::middleware('role:admin,manager,cashier')->group(function () {
        Route::get('/sales',              [SaleController::class, 'index'])       ->name('sales.index');
        Route::get('/sales/create',       [SaleController::class, 'create'])      ->name('sales.create');
        Route::post('/sales',             [SaleController::class, 'store'])       ->name('sales.store');
        Route::get('/sales/{sale}',       [SaleController::class, 'show'])        ->name('sales.show');
        Route::get('/sales/{sale}/pdf',   [SaleController::class, 'downloadPdf']) ->name('sales.pdf');
    });

    // ── Payments (Admin + Manager) ────────────────────────────────
    Route::middleware('role:admin,manager')->group(function () {
        Route::get('/payments',        [PaymentController::class, 'index'])  ->name('payments.index');
        Route::get('/payments/create', [PaymentController::class, 'create']) ->name('payments.create');
        Route::post('/payments',       [PaymentController::class, 'store'])  ->name('payments.store');
    });

    // ── Balances (Admin + Manager) ────────────────────────────────
    Route::middleware('role:admin,manager')->group(function () {
        Route::get('/balances/suppliers', [SupplierBalanceController::class, 'index'])
            ->name('balances.suppliers');

        Route::get('/balances/customers', [CustomerBalanceController::class, 'index'])
            ->name('balances.customers');
    });

    // ── Reports (Admin + Manager) ─────────────────────────────────
    Route::middleware('role:admin,manager')->group(function () {
        Route::get('/reports/sales',      [ReportController::class, 'sales'])     ->name('reports.sales');
        Route::get('/reports/purchases',  [ReportController::class, 'purchases']) ->name('reports.purchases');
        Route::get('/reports/inventory',  [ReportController::class, 'inventory']) ->name('reports.inventory');
        Route::get('/reports/profit',     [ReportController::class, 'profit'])    ->name('reports.profit');
    });

    // ── Settings (Admin only) ─────────────────────────────────────
    Route::middleware('role:admin')->group(function () {
        Route::get('/settings',  [SettingController::class, 'index'])  ->name('settings.index');
        Route::post('/settings', [SettingController::class, 'update']) ->name('settings.update');
    });

    // ── Sale Returns (Admin + Manager + Cashier) ──────────────────
    Route::middleware('role:admin,manager,cashier')->group(function () {
        Route::get('/returns/sales',                      [SaleReturnController::class, 'index'])  ->name('returns.sales.index');
        Route::get('/returns/sales/{sale}/create',        [SaleReturnController::class, 'create']) ->name('returns.sales.create');
        Route::post('/returns/sales/{sale}',              [SaleReturnController::class, 'store'])  ->name('returns.sales.store');
        Route::get('/returns/sales/show/{saleReturn}',    [SaleReturnController::class, 'show'])   ->name('returns.sales.show');
    });

    // ── Purchase Returns (Admin + Manager) ────────────────────────
    Route::middleware('role:admin,manager')->group(function () {
        Route::get('/returns/purchases',                          [PurchaseReturnController::class, 'index'])  ->name('returns.purchases.index');
        Route::get('/returns/purchases/{purchase}/create',        [PurchaseReturnController::class, 'create']) ->name('returns.purchases.create');
        Route::post('/returns/purchases/{purchase}',              [PurchaseReturnController::class, 'store'])  ->name('returns.purchases.store');
        Route::get('/returns/purchases/show/{purchaseReturn}',    [PurchaseReturnController::class, 'show'])   ->name('returns.purchases.show');
    });

    // ── Purchases (Admin + Manager) ── PDF download ───────────────
    Route::middleware('role:admin,manager')->group(function () {
        Route::get('/purchases/{purchase}/pdf', [PurchaseController::class, 'downloadPdf'])->name('purchases.pdf');
    });

});