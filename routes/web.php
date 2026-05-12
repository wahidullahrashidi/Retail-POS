<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\POSController;
use App\Http\Controllers\ShiftController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SalesController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\BackupController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\LanguageController;



Route::get('/', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/login/pin', [AuthController::class, 'pinLogin'])->name('login.pin');


Route::get('/language/{lang}', [LanguageController::class, 'switch'])
    ->name('language.switch');



Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::prefix('shift')->name('shift.')->group(function () {
        Route::get('/open', [ShiftController::class, 'openForm'])->name('open.form');
        Route::post('/open', [ShiftController::class, 'open'])->name('open');
        Route::get('/close', [ShiftController::class, 'closeForm'])->name('close.form');
        Route::post('/close', [ShiftController::class, 'close'])->name('close');
        Route::get('/report/{shift}', [ShiftController::class, 'report'])->name('report');
    });

    Route::middleware('active.shift')->prefix('pos')->name('pos.')->group(function () {
        // dashboard controller routes: 
        Route::get('/dashboard',          [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/products/search',    [DashboardController::class, 'searchProducts'])->name('products.search');
        Route::get('/products/trending',  [DashboardController::class, 'trendingProducts'])->name('products.trending');
        Route::post('/checkout',          [DashboardController::class, 'checkout'])->name('checkout');

        Route::get('/shifts/page', [ShiftController::class, 'page'])->name('shifts.page');
        Route::get('/shifts', [ShiftController::class, 'index'])->name('shifts.index');
        Route::get('/shifts/{shift}/detail', [ShiftController::class, 'detail'])->name('shifts.detail');
        Route::get('/shifts/{shift}/report', [ShiftController::class, 'report'])->name('shifts.report');

        // Redirect to open shift form if trying to close when no active shift
        Route::get('/shift/close', function () {
            $shift = \App\Models\Shift::where('user_id', auth()->id())
                ->where('is_closed', false)->first();
            if (!$shift) return redirect()->route('shift.open.form')
                ->with('error', 'No active shift found.');
            return app(\App\Http\Controllers\ShiftController::class)->closeForm();
        })->name('shift.close.form');



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

        // sales controller routes:
        // ── Sales ──────────────────────────────────────────────

        Route::get('/sales/page',          [SalesController::class, 'page'])->name('sales.page');
        Route::get('/sales',               [SalesController::class, 'index'])->name('sales.index');
        Route::get('/sales/export',        [SalesController::class, 'export'])->name('sales.export');
        Route::post('/sales/refund',       [SalesController::class, 'refund'])->name('sales.refund');
        // Route::delete('/sales/{sale}',     [SalesController::class, 'destroy'])->name('sales.destroy');
        Route::get('/sales/{sale}/items',  [SalesController::class, 'items'])->name('sales.items');


        // supplier controller"
        Route::get('/suppliers/page',                  [SupplierController::class, 'page'])->name('suppliers.page');
        Route::get('/suppliers',                       [SupplierController::class, 'index'])->name('suppliers.index');
        Route::post('/suppliers/store',                [SupplierController::class, 'store'])->name('suppliers.store');
        Route::post('/suppliers/{supplier}/toggle',    [SupplierController::class, 'toggle'])->name('suppliers.toggle');
        Route::get('/suppliers/{supplier}/purchases',  [SupplierController::class, 'supplierPurchases'])->name('suppliers.purchases');

        // ── Purchases ──────────────────────────────────────────
        Route::get('/purchases',                       [PurchaseController::class, 'index'])->name('purchases.index');
        Route::post('/purchases/receive',              [PurchaseController::class, 'receive'])->name('purchases.receive');
        Route::post('/purchases/payment',              [PurchaseController::class, 'storepayment'])->name('purchases.payment');
        Route::get('/purchases/{purchase}/items',      [PurchaseController::class, 'items'])->name('purchases.items');
        Route::post('/purchases/{purchase}/cancel',    [PurchaseController::class, 'cancel'])->name('purchases.cancel');
        // ── Backup & Sync ──────────────────────────────────────
        Route::get('/backup',              [BackupController::class, 'page'])->name('backup');
        Route::get('/backup/status',       [BackupController::class, 'status'])->name('backup.status');
        Route::get('/backup/list',         [BackupController::class, 'list'])->name('backup.list');
        Route::get('/backup/download',     [BackupController::class, 'download'])->name('backup.download');
        Route::post('/backup/run',         [BackupController::class, 'run'])->name('backup.run');
        Route::post('/backup/restore',     [BackupController::class, 'restore'])->name('backup.restore');
        Route::post('/backup/delete',      [BackupController::class, 'delete'])->name('backup.delete');
        Route::post('/backup/sync',        [BackupController::class, 'sync'])->name('backup.sync');
        Route::post('/backup/schedule',    [BackupController::class, 'saveSchedule'])->name('backup.schedule');
        Route::post('/backup/cloud',       [BackupController::class, 'saveCloud'])->name('backup.cloud');
        Route::post('/backup/cloud/test',  [BackupController::class, 'testCloud'])->name('backup.cloud.test');

        // ── Users ──────────────────────────────────────────────

        Route::get('/users/page',              [UserController::class, 'page'])->name('users.page');
        Route::get('/users',                   [UserController::class, 'index'])->name('users.index');
        Route::post('/users/store',            [UserController::class, 'store'])->name('users.store');
        Route::post('/users/password',         [UserController::class, 'resetPassword'])->name('users.password');
        Route::get('/users/{user}/detail',     [UserController::class, 'detail'])->name('users.detail');
        Route::post('/users/{user}/toggle',    [UserController::class, 'toggle'])->name('users.toggle');

        // ── Settings ──────────────────────────────────────────

        Route::get('/settings',                                    [SettingsController::class, 'page'])->name('settings');
        Route::get('/settings/data',                               [SettingsController::class, 'index'])->name('settings.index');
        Route::post('/settings/save',                              [SettingsController::class, 'save'])->name('settings.save');

        // Categories
        Route::get('/settings/categories',                         [SettingsController::class, 'categoriesIndex'])->name('settings.categories.index');
        Route::post('/settings/categories/store',                  [SettingsController::class, 'categoryStore'])->name('settings.categories.store');
        Route::delete('/settings/categories/{category}',           [SettingsController::class, 'categoryDelete'])->name('settings.categories.delete');

        // Attributes
        Route::get('/settings/attributes',                         [SettingsController::class, 'attributesIndex'])->name('settings.attributes.index');
        Route::post('/settings/attributes/store',                  [SettingsController::class, 'attributeStore'])->name('settings.attributes.store');
        Route::delete('/settings/attributes/{attribute}',          [SettingsController::class, 'attributeDelete'])->name('settings.attributes.delete');

        // Attribute values
        Route::post('/settings/attributes/values/store',           [SettingsController::class, 'valueStore'])->name('settings.attributes.values.store');
        Route::delete('/settings/attributes/values/{value}',       [SettingsController::class, 'valueDelete'])->name('settings.attributes.values.delete');

        // Hardware & Audit
        Route::post('/settings/hardware/test',                     [SettingsController::class, 'hardwareTest'])->name('settings.hardware.test');
        Route::get('/settings/audit',                              [SettingsController::class, 'auditLog'])->name('settings.audit');
        // just for error prevention:
        Route::get('/aa', [POSController::class, 'f'])->name('search');
    });
});
