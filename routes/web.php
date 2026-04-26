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
        Route::get('/', [POSController::class, 'index'])->name('index');
        Route::get('/customers', [POSController::class, 'showAllCustomers'])->name('allCustomers');
        Route::get('/search', [POSController::class, 'searchProduct'])->name('search');
        Route::get('/barcode/{barcode}', [POSController::class, 'searchByBarcode'])->name('barcode');
    });
});
