<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\POSController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ShiftController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InvenotoryController;

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
        Route::get('/dashboard',          [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/products/search',    [DashboardController::class, 'searchProducts'])->name('products.search');
        Route::get('/products/trending',  [DashboardController::class, 'trendingProducts'])->name('products.trending');

        // pos controller routes:
        Route::post('/checkout',          [DashboardController::class, 'checkout'])->name('checkout');

        // routes for sidebar views: just for practice.
        Route::get('/pos_checkout', [POSController::class, 'index'])->name('poscheck');
        Route::resource('/Inventory', InvenotoryController::class);


        // routes for testing:

        Route::post('/pos/checkout/store', [PosController::class, 'store'])->name('checkout.store');
        Route::post('/pos/checkout/hold',  [PosController::class, 'hold'])->name('checkout.hold');
        Route::get('/pos/customers/search', [PosController::class, 'searchCustomers'])->name('customers.search');
        
        // just for error prevention:
        Route::get('/aa', [POSController::class, 'f'])->name('search');
    });
});
