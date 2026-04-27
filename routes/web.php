<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\POSController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ShiftController;
use Illuminate\Support\Facades\Route;

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
        // Route::get('/dashboard', [POSController::class, 'index'])->name('index');
        // Route::get('/dashboard/search', [POSController::class, 'search'])->name('dashboard.search');
        // web.php
        // Route::get('/products/search', [POSController::class, 'searchProducts'])->name('products.search');
        // Route::get('/products/trending', [POSController::class, 'trendingProducts'])->name('products.trending');
        // Route::post('/checkout', [POSController::class, 'store'])->name('checkout');
        Route::get('/dashboard',          [PosController::class, 'index'])->name('dashboard');
        Route::get('/products/search',    [PosController::class, 'searchProducts'])->name('products.search');
        Route::get('/products/trending',  [PosController::class, 'trendingProducts'])->name('products.trending');
        Route::post('/checkout',          [PosController::class, 'checkout'])->name('checkout');

        Route::get('/customers', [POSController::class, 'showAllCustomers'])->name('allCustomers');
        Route::get('/search', [POSController::class, 'searchProduct'])->name('search');
        Route::get('/barcode/{barcode}', [POSController::class, 'searchByBarcode'])->name('barcode');
    });
});
