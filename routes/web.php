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
        Route::post('/checkout',          [DashboardController::class, 'checkout'])->middleware('permission:pos.sale')->name('checkout');

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
        Route::post('/checkout/store',  [PosController::class, 'store'])->middleware('permission:pos.sale')->name('checkout.store');
        Route::post('/checkout/hold',   [PosController::class, 'hold'])->middleware('permission:pos.hold')->name('checkout.hold');
        Route::get('/customers/search', [PosController::class, 'searchCustomers'])->name('customers.search');
        Route::get('/checkout/recall',  [PosController::class, 'recall'])->middleware('permission:pos.sale,pos.hold')->name('checkout.recall');


        // inventory controller routes: 
        Route::get('/inventory',                            [InventoryController::class, 'page'])->middleware('permission:inventory.*')->name('inventory');
        Route::get('/inventory/products',                   [InventoryController::class, 'index'])->middleware('permission:inventory.*')->name('inventory.products');
        Route::post('/inventory/products/store',            [InventoryController::class, 'store'])->middleware('permission:inventory.*')->name('inventory.products.store');
        Route::post('/inventory/products/{variant}/toggle', [InventoryController::class, 'toggle'])->middleware('permission:inventory.*')->name('inventory.products.toggle');
        Route::post('/inventory/adjust',                    [InventoryController::class, 'adjust'])->middleware('permission:inventory.*')->name('inventory.adjust');
        Route::post('/inventory/purchase/store',            [InventoryController::class, 'storePurchase'])->middleware('permission:inventory.*')->name('inventory.purchase.store');

        // customer controller routes::
        //
        // Route::post('/customers', [CutomersController::class, 'storeCustomer'])->name('customers.store');
        Route::get('/customers/page',           [CustomerController::class, 'page'])->middleware('permission:customers.*,loan.payment,pos.sale')->name('customers.page');
        Route::get('/customers',                [CustomerController::class, 'index'])->middleware('permission:customers.*,loan.payment,pos.sale')->name('customers.index');
        Route::get('/customers/search',         [CustomerController::class, 'search'])->name('customers.search');
        Route::get('/customers/export',         [CustomerController::class, 'export'])->middleware('permission:customers.*,reports.*')->name('customers.export');
        Route::post('/customers/store',         [CustomerController::class, 'store'])->middleware('permission:customers.*,pos.sale')->name('customers.store');
        Route::post('/customers/payment',       [CustomerController::class, 'payment'])->middleware('permission:loan.payment')->name('customers.payment');
        Route::get('/customers/{customer}/detail', [CustomerController::class, 'detail'])->middleware('permission:customers.*,loan.payment,pos.sale')->name('customers.detail');
        Route::get('/customers/{customer}/loan',   [CustomerController::class, 'loan'])->middleware('permission:customers.*,loan.payment')->name('customers.loan');
        Route::post('/customers/{customer}/toggle', [CustomerController::class, 'toggle'])->middleware('permission:customers.*')->name('customers.toggle');


        // Report Controller:
        // ── Reports ──────────────────────────────────────────
        Route::get('/AZSdfghreports',   [ReportController::class, 'page'])->middleware('permission:reports.*')->name('reports');
        Route::get('/reports/data',     [ReportController::class, 'data'])->middleware('permission:reports.*')->name('reports.data');
        Route::get('/reports/zreport',  [ReportController::class, 'zreport'])->middleware('permission:reports.*')->name('reports.zreport');
        Route::get('/reports/export',   [ReportController::class, 'export'])->middleware('permission:reports.*')->name('reports.export');
        // Route::get('/report', [ReportController::class, 'page'])->name('reports');

        // sales controller routes:
        // ── Sales ──────────────────────────────────────────────

        Route::get('/sales/page',          [SalesController::class, 'page'])->middleware('permission:pos.sale,reports.*')->name('sales.page');
        Route::get('/sales',               [SalesController::class, 'index'])->middleware('permission:pos.sale,reports.*')->name('sales.index');
        Route::get('/sales/export',        [SalesController::class, 'export'])->middleware('permission:reports.*')->name('sales.export');
        Route::post('/sales/refund',       [SalesController::class, 'refund'])->middleware('permission:pos.return')->name('sales.refund');
        // Route::delete('/sales/{sale}',     [SalesController::class, 'destroy'])->name('sales.destroy');
        Route::get('/sales/{sale}/items',  [SalesController::class, 'items'])->middleware('permission:pos.sale,reports.*')->name('sales.items');


        // supplier controller"
        Route::get('/suppliers/page',                  [SupplierController::class, 'page'])->middleware('permission:inventory.*')->name('suppliers.page');
        Route::get('/suppliers',                       [SupplierController::class, 'index'])->middleware('permission:inventory.*')->name('suppliers.index');
        Route::post('/suppliers/store',                [SupplierController::class, 'store'])->middleware('permission:inventory.*')->name('suppliers.store');
        Route::post('/suppliers/{supplier}/toggle',    [SupplierController::class, 'toggle'])->middleware('permission:inventory.*')->name('suppliers.toggle');
        Route::get('/suppliers/{supplier}/purchases',  [SupplierController::class, 'supplierPurchases'])->middleware('permission:inventory.*')->name('suppliers.purchases');

        // ── Purchases ──────────────────────────────────────────
        Route::get('/purchases',                       [PurchaseController::class, 'index'])->middleware('permission:inventory.*')->name('purchases.index');
        Route::post('/purchases/receive',              [PurchaseController::class, 'receive'])->middleware('permission:inventory.*')->name('purchases.receive');
        Route::post('/purchases/payment',              [PurchaseController::class, 'storepayment'])->middleware('permission:inventory.*')->name('purchases.payment');
        Route::get('/purchases/{purchase}/items',      [PurchaseController::class, 'items'])->middleware('permission:inventory.*')->name('purchases.items');
        Route::post('/purchases/{purchase}/cancel',    [PurchaseController::class, 'cancel'])->middleware('permission:inventory.*')->name('purchases.cancel');
        // ── Backup & Sync ──────────────────────────────────────
        Route::get('/backup',              [BackupController::class, 'page'])->middleware('permission:backup.*')->name('backup');
        Route::get('/backup/status',       [BackupController::class, 'status'])->middleware('permission:backup.*')->name('backup.status');
        Route::get('/backup/list',         [BackupController::class, 'list'])->middleware('permission:backup.*')->name('backup.list');
        Route::get('/backup/download',     [BackupController::class, 'download'])->middleware('permission:backup.*')->name('backup.download');
        Route::post('/backup/run',         [BackupController::class, 'run'])->middleware('permission:backup.*')->name('backup.run');
        Route::post('/backup/restore',     [BackupController::class, 'restore'])->middleware('permission:backup.*')->name('backup.restore');
        Route::post('/backup/delete',      [BackupController::class, 'delete'])->middleware('permission:backup.*')->name('backup.delete');
        Route::post('/backup/sync',        [BackupController::class, 'sync'])->middleware('permission:backup.*')->name('backup.sync');
        Route::post('/backup/schedule',    [BackupController::class, 'saveSchedule'])->middleware('permission:backup.*')->name('backup.schedule');
        Route::post('/backup/cloud',       [BackupController::class, 'saveCloud'])->middleware('permission:backup.*')->name('backup.cloud');
        Route::post('/backup/cloud/test',  [BackupController::class, 'testCloud'])->middleware('permission:backup.*')->name('backup.cloud.test');
        Route::get('/backup/cloud/quota',  [BackupController::class, 'cloudQuota'])->middleware('permission:backup.*')->name('backup.cloud.quota');
        Route::get('/backup/dropbox/quota',[BackupController::class, 'dropboxQuota'])->middleware('permission:backup.*')->name('backup.dropbox.quota');
        // ── Users ──────────────────────────────────────────────

        Route::get('/users/page',              [UserController::class, 'page'])->middleware('permission:users.*')->name('users.page');
        Route::get('/users',                   [UserController::class, 'index'])->middleware('permission:users.*')->name('users.index');
        Route::post('/users/store',            [UserController::class, 'store'])->middleware('permission:users.*')->name('users.store');
        Route::post('/users/password',         [UserController::class, 'resetPassword'])->middleware('permission:users.*')->name('users.password');
        Route::get('/users/{user}/detail',     [UserController::class, 'detail'])->middleware('permission:users.*')->name('users.detail');
        Route::post('/users/{user}/toggle',    [UserController::class, 'toggle'])->middleware('permission:users.*')->name('users.toggle');

        // ── Settings ──────────────────────────────────────────

        Route::get('/settings',                                    [SettingsController::class, 'page'])->middleware('permission:settings.*')->name('settings');
        Route::get('/settings/data',                               [SettingsController::class, 'index'])->middleware('permission:settings.*')->name('settings.index');
        Route::post('/settings/save',                              [SettingsController::class, 'save'])->middleware('permission:settings.*')->name('settings.save');

        // Categories
        Route::get('/settings/categories',                         [SettingsController::class, 'categoriesIndex'])->middleware('permission:settings.*')->name('settings.categories.index');
        Route::post('/settings/categories/store',                  [SettingsController::class, 'categoryStore'])->middleware('permission:settings.*')->name('settings.categories.store');
        Route::delete('/settings/categories/{category}',           [SettingsController::class, 'categoryDelete'])->middleware('permission:settings.*')->name('settings.categories.delete');

        // Attributes
        Route::get('/settings/attributes',                         [SettingsController::class, 'attributesIndex'])->middleware('permission:settings.*')->name('settings.attributes.index');
        Route::post('/settings/attributes/store',                  [SettingsController::class, 'attributeStore'])->middleware('permission:settings.*')->name('settings.attributes.store');
        Route::delete('/settings/attributes/{attribute}',          [SettingsController::class, 'attributeDelete'])->middleware('permission:settings.*')->name('settings.attributes.delete');

        // Attribute values
        Route::post('/settings/attributes/values/store',           [SettingsController::class, 'valueStore'])->middleware('permission:settings.*')->name('settings.attributes.values.store');
        Route::delete('/settings/attributes/values/{value}',       [SettingsController::class, 'valueDelete'])->middleware('permission:settings.*')->name('settings.attributes.values.delete');

        // Hardware & Audit
        Route::post('/settings/hardware/test',                     [SettingsController::class, 'hardwareTest'])->middleware('permission:settings.*')->name('settings.hardware.test');
        Route::get('/settings/audit',                              [SettingsController::class, 'auditLog'])->middleware('permission:settings.*')->name('settings.audit');
        // just for error prevention:
        Route::get('/aa', [POSController::class, 'f'])->name('search');
    });
});
