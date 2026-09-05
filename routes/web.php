<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\StockAdjustmentController;
use App\Http\Controllers\StockMovementController;
use App\Http\Controllers\PaymentController;
// =========================
// Authentication
// =========================

Route::get('/login', [AuthController::class, 'showLogin'])
    ->name('login');

Route::post('/login', [AuthController::class, 'login'])
    ->name('login.submit');

Route::post('/logout', [AuthController::class, 'logout'])
    ->name('logout');


// =========================
// Authenticated Routes
// =========================

Route::middleware('auth')->group(function () {

    // Dashboard
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');


    Route::middleware('role:admin')->group(function () {
        Route::resource('users', UserController::class);
    });

    // Categories - Admin + Manager
    Route::middleware('role:admin,manager')->group(function () {
        Route::resource('categories', CategoryController::class);
    });

    Route::middleware('role:admin,manager')->group(function () {
        Route::resource('products', ProductController::class);
    });

    Route::middleware('role:admin,manager')->group(function () {
    Route::resource('suppliers', SupplierController::class);
    });

Route::middleware('role:admin,manager,cashier')->group(function () {
    Route::resource('customers', CustomerController::class);
});

Route::middleware('role:admin,manager')->group(function () {
    Route::get('/inventory', [InventoryController::class, 'index'])
        ->name('inventory.index');
});


Route::middleware('role:admin,manager')->group(function () {

    Route::get('/inventory/adjustment', [
        StockAdjustmentController::class,
        'create'
    ])->name('inventory.adjustment.create');

    Route::post('/inventory/adjustment', [
        StockAdjustmentController::class,
        'store'
    ])->name('inventory.adjustment.store');

});

Route::get('/inventory/movements', [
    StockMovementController::class,
    'index'
])->name('inventory.movements');

Route::get('/inventory/low-stock', [
    InventoryController::class,
    'lowStock'
])->name('inventory.low-stock');

Route::middleware('role:admin,manager')->group(function () {

    Route::get('/payments', [
        PaymentController::class,
        'index'
    ])->name('payments.index');

    Route::get('/payments/create', [
        PaymentController::class,
        'create'
    ])->name('payments.create');

    Route::post('/payments', [
        PaymentController::class,
        'store'
    ])->name('payments.store');

});
    // =========================
    // Purchases
    // Admin + Manager
    // =========================

    Route::middleware('role:admin,manager')->group(function () {

        Route::post('/purchases', [PurchaseController::class, 'store'])
            ->name('purchases.store');

        Route::get('/purchases', [PurchaseController::class, 'index'])
            ->name('purchases.index');

        Route::get('/purchases/create', [PurchaseController::class, 'create'])
            ->name('purchases.create');

        Route::get('/purchases/{purchase}', [PurchaseController::class, 'show'])
            ->name('purchases.show');
    });


    // =========================
    // Sales
    // Admin + Manager + Cashier
    // =========================

    Route::middleware('role:admin,manager,cashier')->group(function () {

        Route::get('/sales', [SaleController::class, 'index'])
            ->name('sales.index');

        Route::post('/sales', [SaleController::class, 'store'])
            ->name('sales.store');

        Route::get('/sales/create', [SaleController::class, 'create'])
            ->name('sales.create');

        Route::get('/sales/{sale}', [SaleController::class, 'show'])
            ->name('sales.show');
    });

});