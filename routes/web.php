<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\POSController;
use App\Http\Controllers\ShiftController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ReportController;

Route::get('/', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/login/pin', [AuthController::class, 'pinLogin'])->name('login.pin');

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::prefix('shift')->name('shift.')->group(function () {
        Route::get('/open', [ShiftController::class, 'openForm'])->name('open.form');
        Route::post('/open', [ShiftController::class, 'open'])->name('open');
        Route::get('/close', [ShiftController::class, 'closeForm'])->name('close.form');
        Route::post('/close', [ShiftController::class, 'close'])->name('close');
        Route::get('/report/{id}', [ShiftController::class, 'report'])->name('report');
    });

    Route::middleware('active.shift')->prefix('pos')->name('pos.')->group(function () {
        // dashboard controller routes: 
        Route::get('/dashboard',          [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/products/search',    [DashboardController::class, 'searchProducts'])->name('products.search');
        Route::get('/products/trending',  [DashboardController::class, 'trendingProducts'])->name('products.trending');
        Route::post('/checkout',          [DashboardController::class, 'checkout'])->name('checkout');


        // poscontroller routes: 
        Route::get('/pos_checkout',     [POSController::class, 'index'])->name('poscheck');
        Route::post('/checkout/store',  [PosController::class, 'store'])->name('checkout.store');
        Route::post('/checkout/hold',   [PosController::class, 'hold'])->name('checkout.hold');
        Route::get('/customers/search', [PosController::class, 'searchCustomers'])->name('customers.search');
        Route::get('/checkout/recall',  [PosController::class, 'recall'])->name('checkout.recall');


        // inventory controller routes: 
        Route::get('/inventory',                            [InventoryController::class, 'page'])->name('inventory');
        Route::get('/inventory/products',                   [InventoryController::class, 'index'])->name('inventory.products');
        Route::post('/inventory/products/store',            [InventoryController::class, 'store'])->name('inventory.products.store');
        Route::post('/inventory/products/{variant}/toggle', [InventoryController::class, 'toggle'])->name('inventory.products.toggle');
        Route::post('/inventory/adjust',                    [InventoryController::class, 'adjust'])->name('inventory.adjust');
        Route::post('/inventory/purchase/store',            [InventoryController::class, 'storePurchase'])->name('inventory.purchase.store');

        // customer controller routes::
        //
        // Route::post('/customers', [CutomersController::class, 'storeCustomer'])->name('customers.store');
        Route::get('/customers/page',           [CustomerController::class, 'page'])->name('customers.page');
        Route::get('/customers',                [CustomerController::class, 'index'])->name('customers.index');
        Route::get('/customers/search',         [CustomerController::class, 'search'])->name('customers.search');
        Route::get('/customers/export',         [CustomerController::class, 'export'])->name('customers.export');
        Route::post('/customers/store',         [CustomerController::class, 'store'])->name('customers.store');
        Route::post('/customers/payment',       [CustomerController::class, 'payment'])->name('customers.payment');
        Route::get('/customers/{customer}/detail', [CustomerController::class, 'detail'])->name('customers.detail');
        Route::get('/customers/{customer}/loan',   [CustomerController::class, 'loan'])->name('customers.loan');
        Route::post('/customers/{customer}/toggle', [CustomerController::class, 'toggle'])->name('customers.toggle');


        // Report Controller:
        // ── Reports ──────────────────────────────────────────
        Route::get('/AZSdfghreports',   [ReportController::class, 'page'])->name('reports');
        Route::get('/reports/data',     [ReportController::class, 'data'])->name('reports.data');
        Route::get('/reports/zreport',  [ReportController::class, 'zreport'])->name('reports.zreport');
        Route::get('/reports/export',   [ReportController::class, 'export'])->name('reports.export');
        // Route::get('/report', [ReportController::class, 'page'])->name('reports');
        // just for error prevention:
        Route::get('/aa', [POSController::class, 'f'])->name('search');
    });
});
