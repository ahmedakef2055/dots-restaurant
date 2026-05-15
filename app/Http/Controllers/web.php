<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\EmployeeSalaryAdjustmentController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\KdsController;
use App\Http\Controllers\FinancialController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\CouponController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OfferController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\RestaurantTableController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RecipeController;
use App\Http\Controllers\SalaryController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\WaiterController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::get('/test-firefox', function () {
    $cmd = '/usr/local/bin/wkhtmltoimage --width 576 /tmp/test-ar.html /tmp/test-web.png 2>&1';
    exec($cmd, $out, $ret);
    return response()->json(['ret' => $ret, 'out' => $out]);
});

Route::get('/locale/translations', function (): JsonResponse {
    $flatten = function (array $items, string $prefix = '') use (&$flatten): array {
        $result = [];

        foreach ($items as $key => $value) {
            $fullKey = $prefix === '' ? (string) $key : $prefix . '.' . $key;

            if (is_array($value)) {
                $result = array_merge($result, $flatten($value, $fullKey));
                continue;
            }

            if (! is_string($value) || trim($value) === '') {
                continue;
            }

            $result[$fullKey] = $value;
        }

        return $result;
    };

    $pairs = [];
    $enFiles = glob(lang_path('en/*.php')) ?: [];

    foreach ($enFiles as $enFilePath) {
        $fileName = basename($enFilePath, '.php');
        $arFilePath = lang_path('ar/' . $fileName . '.php');

        if (! is_file($arFilePath)) {
            continue;
        }

        $enPayload = require $enFilePath;
        $arPayload = require $arFilePath;

        if (! is_array($enPayload) || ! is_array($arPayload)) {
            continue;
        }

        $enFlat = $flatten($enPayload);
        $arFlat = $flatten($arPayload);

        foreach ($enFlat as $key => $enValue) {
            $arValue = $arFlat[$key] ?? null;

            if (! is_string($arValue) || trim($arValue) === '') {
                continue;
            }

            $pairs[] = [
                'en' => $enValue,
                'ar' => $arValue,
            ];
        }
    }

    return response()->json([
        'pairs' => $pairs,
    ]);
})->name('locale.translations');

Route::post('/locale', function (Request $request): JsonResponse {
    $validated = $request->validate([
        'locale' => ['required', 'in:en,ar'],
    ]);

    $locale = $validated['locale'];

    $request->session()->put('locale', $locale);
    app()->setLocale($locale);

    return response()
        ->json([
            'message' => __('messages.locale.updated'),
            'locale' => $locale,
        ])
        ->cookie('locale', $locale, 60 * 24 * 365);
})->name('locale.update');

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');

// QZ Tray certificate signing (public - only signs a string, no sensitive data)
Route::get('/qz/sign', function (\Illuminate\Http\Request $request) {
    $msg = $request->query('msg', '');
    $privateKey = openssl_pkey_get_private(file_get_contents(storage_path('app/qz-private-key.pem')));
    openssl_sign($msg, $signature, $privateKey, 'sha512WithRSAEncryption');
    return response(base64_encode($signature))->header('Content-Type', 'text/plain');
})->name('qz.sign');

Route::middleware(['auth.custom', 'permission.required'])->group(function (): void {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::patch('/notifications/read-all', [NotificationController::class, 'markAllRead'])->name('notifications.read-all');
    Route::patch('/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');

    Route::get('/', DashboardController::class)
        ->middleware('permission:dashboard.view')
        ->name('dashboard');

    Route::get('/pos', [PosController::class, 'index'])
        ->middleware('permission:pos.view')
        ->name('pos.index');

    Route::get('/waiter', [WaiterController::class, 'index'])
        ->middleware('permission:waiter.view')
        ->name('waiter.index');

    Route::post('/pos/orders', [PosController::class, 'store'])
        ->middleware('permission:pos.view')
        ->name('pos.orders.store');

    Route::post('/pos/shifts/start', [PosController::class, 'startShift'])
        ->middleware('permission:pos.view')
        ->name('pos.shifts.start');

    Route::post('/pos/shifts/end', [PosController::class, 'endShift'])
        ->middleware('permission:pos.view')
        ->name('pos.shifts.end');

    Route::get('/pos/customers/lookup', [PosController::class, 'lookupCustomers'])
        ->middleware('permission:pos.view')
        ->name('pos.customers.lookup');

    Route::get('/pos/tables/{restaurantTable}/order', [PosController::class, 'tableOrder'])
        ->middleware('permission:pos.view')
        ->name('pos.tables.order');

    Route::post('/pos/orders/{order}/transfer-table', [PosController::class, 'transferTable'])
        ->middleware('permission:pos.view')
        ->name('pos.orders.transfer-table');

    Route::get('/kds', [KdsController::class, 'index'])
        ->middleware('permission:kds.view')
        ->name('kds.index');

    Route::get('/kds/data', [KdsController::class, 'data'])
        ->middleware('permission:kds.view')
        ->name('kds.data');

    Route::patch('/kds/orders/{order}/transition', [KdsController::class, 'transition'])
        ->middleware('permission:kds.view')
        ->name('kds.orders.transition');

    Route::get('/bar', [KdsController::class, 'barIndex'])
        ->middleware('permission:bar.view')
        ->name('bar.index');

    Route::get('/bar/data', [KdsController::class, 'barData'])
        ->middleware('permission:bar.view')
        ->name('bar.data');

    Route::patch('/bar/orders/{order}/transition', [KdsController::class, 'barTransition'])
        ->middleware('permission:bar.view')
        ->name('bar.orders.transition');

    Route::get('/orders', [OrderController::class, 'index'])
        ->middleware('permission:orders.view')
        ->name('orders.index');

    Route::get('/orders/{order}', [OrderController::class, 'show'])
        ->middleware('permission:orders.view')
        ->name('orders.show');

    Route::patch('/orders/{order}/status', [OrderController::class, 'updateStatus'])
        ->middleware('permission:orders.update')
        ->name('orders.status.update');

    Route::patch('/orders/{order}/discount', [OrderController::class, 'updateDiscount'])
        ->middleware('permission:orders.update')
        ->name('orders.discount.update');

    Route::delete('/orders/{order}/items/{orderItem}', [OrderController::class, 'destroyItem'])
        ->middleware('permission:orders.delete')
        ->name('orders.items.destroy');

    Route::patch('/orders/{order}/items/{orderItem}/quantity', [OrderController::class, 'updateItemQuantity'])
        ->middleware('permission:orders.update')
        ->name('orders.items.quantity.update');

    Route::delete('/orders/{order}', [OrderController::class, 'destroy'])
        ->middleware('permission:orders.delete')
        ->name('orders.destroy');

    Route::get('/orders/{order}/invoice', [OrderController::class, 'invoice'])
        ->middleware('permission:orders.view')
        ->name('orders.invoice');
    Route::post('/orders/{order}/direct-print', [OrderController::class, 'directPrint'])
        ->name('orders.direct-print');
    Route::post('/orders/{order}/queue-print', [OrderController::class, 'queuePrint'])
        ->middleware('permission:orders.view')
        ->name('orders.queue-print');
    Route::get('/orders/{order}/receipt-data', [OrderController::class, 'receiptData'])
        ->middleware('permission:orders.view')
        ->name('orders.receipt-data');

    // Print Job Queue (QZ Tray polling)
    Route::get('/print-jobs/next', [App\Http\Controllers\PrintJobController::class, 'next'])
        ->middleware('permission:orders.view')
        ->name('print-jobs.next');
    Route::patch('/print-jobs/{printJob}/done', [App\Http\Controllers\PrintJobController::class, 'done'])
        ->name('print-jobs.done');
    Route::patch('/print-jobs/{printJob}/failed', [App\Http\Controllers\PrintJobController::class, 'failed'])
        ->name('print-jobs.failed');
    Route::get('/print-jobs/{printJob}/status', [App\Http\Controllers\PrintJobController::class, 'status'])
        ->name('print-jobs.status');

    Route::get('/test-print', function () {
        $dev = '/dev/usb/lp0';
        $info = [
            'php_version'    => PHP_VERSION,
            'sapi'           => PHP_SAPI,
            'process_uid'    => posix_getuid(),
            'process_user'   => posix_getpwuid(posix_getuid())['name'] ?? '?',
            'process_groups' => array_map(fn($g) => posix_getgrgid($g)['name'] ?? $g, posix_getgroups()),
            'device'         => $dev,
            'file_exists'    => file_exists($dev),
            'is_readable'    => is_readable($dev),
            'is_writable'    => is_writable($dev),
            'stat'           => @stat($dev) ?: 'stat() failed',
            'fopen_test'     => (function() use ($dev) {
                $f = @fopen($dev, 'wb');
                if ($f) { fclose($f); return 'OK — fopen succeeded'; }
                return 'FAILED: ' . (error_get_last()['message'] ?? 'unknown');
            })(),
            'glob_usb'       => glob('/dev/usb/*') ?: [],
        ];
        return response()->json($info, 200, [], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    })->name('test.print.diag');

    Route::get('/reports/shift-logs', [ReportController::class, 'shiftLogs'])
        ->middleware('permission:reports.view')
        ->name('reports.shift-logs');

    Route::get('/reports/shift-logs/export/pdf', [ReportController::class, 'shiftLogsExportPdf'])
        ->middleware('permission:reports.view')
        ->name('reports.shift-logs.exportPdf');

    Route::get('/reports/shift-logs/{shiftLog}', [ReportController::class, 'shiftLogProfile'])
        ->middleware('permission:reports.view')
        ->name('reports.shift-logs.profile');

    Route::get('/reports/shift-logs/{shiftLog}/receipt', [ReportController::class, 'shiftLogReceipt'])
        ->middleware('permission:reports.view')
        ->name('reports.shift-logs.receipt');
    Route::post('/reports/shift-logs/{shiftLog}/direct-print', [ReportController::class, 'directPrint'])
        ->name('reports.shift-logs.direct-print');

    Route::get('/inventory', [InventoryController::class, 'index'])
        ->middleware('permission:inventory.view')
        ->name('inventory.index');

    Route::get('/inventory/logs/pdf', [InventoryController::class, 'exportLogsPdf'])
        ->middleware('permission:inventory.view')
        ->name('inventory.logs.pdf');

    Route::get('/inventory/create', [InventoryController::class, 'create'])
        ->middleware('permission:inventory.create')
        ->name('inventory.create');

    Route::post('/inventory', [InventoryController::class, 'store'])
        ->middleware('permission:inventory.create')
        ->name('inventory.store');

    Route::get('/inventory/{ingredient}/edit', [InventoryController::class, 'edit'])
        ->middleware('permission:inventory.update')
        ->name('inventory.edit');

    Route::put('/inventory/{ingredient}', [InventoryController::class, 'update'])
        ->middleware('permission:inventory.update')
        ->name('inventory.update');

    Route::get('/inventory/{ingredient}/adjust', [InventoryController::class, 'adjustForm'])
        ->middleware('permission:inventory.adjust')
        ->name('inventory.adjust.form');

    Route::post('/inventory/{ingredient}/adjust', [InventoryController::class, 'adjustStock'])
        ->middleware('permission:inventory.adjust')
        ->name('inventory.adjust.stock');

    Route::post('/inventory/transfer', [InventoryController::class, 'transfer'])
        ->middleware('permission:inventory.adjust')
        ->name('inventory.transfer');

    Route::post('/inventory/warehouses', [InventoryController::class, 'storeWarehouse'])
        ->middleware('permission:inventory.update')
        ->name('inventory.warehouses.store');

    Route::put('/inventory/warehouses/{warehouse}', [InventoryController::class, 'updateWarehouse'])
        ->middleware('permission:inventory.update')
        ->name('inventory.warehouses.update');

    Route::post('/inventory/audit', [InventoryController::class, 'audit'])
        ->middleware('permission:inventory.audit')
        ->name('inventory.audit');

    Route::delete('/inventory/{ingredient}', [InventoryController::class, 'destroy'])
        ->middleware('permission:inventory.delete')
        ->name('inventory.destroy');

    Route::get('/recipes', [RecipeController::class, 'index'])
        ->middleware('permission:recipes.view')
        ->name('recipes.index');

    Route::get('/recipes/pdf', [RecipeController::class, 'exportPdf'])
        ->middleware('permission:recipes.view')
        ->name('recipes.pdf');

    Route::get('/recipes/create', [RecipeController::class, 'create'])
        ->middleware('permission:recipes.create')
        ->name('recipes.create');

    Route::get('/recipes/{product}/edit', [RecipeController::class, 'edit'])
        ->middleware('permission:recipes.update')
        ->name('recipes.edit');

    Route::put('/recipes/{product}', [RecipeController::class, 'update'])
        ->middleware('permission:recipes.update')
        ->name('recipes.update');

    Route::delete('/recipes/{product}', [RecipeController::class, 'destroy'])
        ->middleware('permission:recipes.delete')
        ->name('recipes.destroy');

    Route::get('/recipes/semi-finished/create', [RecipeController::class, 'createSemiFinished'])
        ->middleware('permission:recipes.create')
        ->name('recipes.semi-finished.create');

    Route::post('/recipes/semi-finished', [RecipeController::class, 'storeSemiFinished'])
        ->middleware('permission:recipes.create')
        ->name('recipes.semi-finished.store');

    Route::get('/recipes/semi-finished/{recipeVersion}/edit', [RecipeController::class, 'editSemiFinished'])
        ->middleware('permission:recipes.update')
        ->name('recipes.semi-finished.edit');

    Route::put('/recipes/semi-finished/{recipeVersion}', [RecipeController::class, 'updateSemiFinished'])
        ->middleware('permission:recipes.update')
        ->name('recipes.semi-finished.update');

    Route::delete('/recipes/semi-finished/{recipeVersion}', [RecipeController::class, 'destroySemiFinished'])
        ->middleware('permission:recipes.delete')
        ->name('recipes.semi-finished.destroy');

    Route::get('/tables', [RestaurantTableController::class, 'index'])
        ->middleware('permission:tables.view')
        ->name('tables.index');
    Route::get('/tables/create', [RestaurantTableController::class, 'create'])
        ->middleware('permission:tables.create')
        ->name('tables.create');
    Route::post('/tables', [RestaurantTableController::class, 'store'])
        ->middleware('permission:tables.create')
        ->name('tables.store');
    Route::get('/tables/{restaurantTable}/edit', [RestaurantTableController::class, 'edit'])
        ->middleware('permission:tables.update')
        ->name('tables.edit');
    Route::put('/tables/{restaurantTable}', [RestaurantTableController::class, 'update'])
        ->middleware('permission:tables.update')
        ->name('tables.update');
    Route::patch('/tables/{restaurantTable}/toggle-status', [RestaurantTableController::class, 'toggleStatus'])
        ->middleware('permission:tables.update')
        ->name('tables.toggle-status');
    Route::delete('/tables/{restaurantTable}', [RestaurantTableController::class, 'destroy'])
        ->middleware('permission:tables.delete')
        ->name('tables.destroy');

    Route::get('/categories', [CategoryController::class, 'index'])
        ->middleware('permission:categories.view')
        ->name('categories.index');
    Route::get('/categories/create', [CategoryController::class, 'create'])
        ->middleware('permission:categories.create')
        ->name('categories.create');
    Route::post('/categories', [CategoryController::class, 'store'])
        ->middleware('permission:categories.create')
        ->name('categories.store');
    Route::get('/categories/{category}/edit', [CategoryController::class, 'edit'])
        ->middleware('permission:categories.update')
        ->name('categories.edit');
    Route::put('/categories/{category}', [CategoryController::class, 'update'])
        ->middleware('permission:categories.update')
        ->name('categories.update');
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])
        ->middleware('permission:categories.delete')
        ->name('categories.destroy');

    Route::get('/products', [ProductController::class, 'index'])
        ->middleware('permission:products.view')
        ->name('products.index');
    Route::get('/products/create', [ProductController::class, 'create'])
        ->middleware('permission:products.create')
        ->name('products.create');
    Route::post('/products', [ProductController::class, 'store'])
        ->middleware('permission:products.create')
        ->name('products.store');
    Route::get('/products/{product}/edit', [ProductController::class, 'edit'])
        ->middleware('permission:products.update')
        ->name('products.edit');
    Route::put('/products/{product}', [ProductController::class, 'update'])
        ->middleware('permission:products.update')
        ->name('products.update');
    Route::delete('/products/{product}', [ProductController::class, 'destroy'])
        ->middleware('permission:products.delete')
        ->name('products.destroy');

    Route::get('/customers', [CustomerController::class, 'index'])
        ->middleware('permission:customers.view')
        ->name('customers.index');
    Route::get('/customers/pdf', [CustomerController::class, 'exportPdf'])
        ->middleware('permission:customers.view')
        ->name('customers.pdf');

    Route::get('/customers/create', [CustomerController::class, 'create'])
        ->middleware('permission:customers.create')
        ->name('customers.create');

    Route::post('/customers', [CustomerController::class, 'store'])
        ->middleware('permission:customers.create')
        ->name('customers.store');

    Route::get('/customers/{customer}', [CustomerController::class, 'show'])
        ->middleware('permission:customers.view')
        ->name('customers.show');

    Route::get('/customers/{customer}/edit', [CustomerController::class, 'edit'])
        ->middleware('permission:customers.update')
        ->name('customers.edit');

    Route::put('/customers/{customer}', [CustomerController::class, 'update'])
        ->middleware('permission:customers.update')
        ->name('customers.update');

    Route::delete('/customers/{customer}', [CustomerController::class, 'destroy'])
        ->middleware('permission:customers.delete')
        ->name('customers.destroy');

    Route::get('/users', [UserManagementController::class, 'index'])
        ->middleware('permission:users.view')
        ->name('users.index');

    Route::get('/users/create', [UserManagementController::class, 'create'])
        ->middleware('permission:users.create')
        ->name('users.create');

    Route::get('/users/{user}/edit', [UserManagementController::class, 'edit'])
        ->middleware('permission:users.update')
        ->name('users.edit');

    Route::get('/users/{user}', [UserManagementController::class, 'show'])
        ->middleware('permission:users.view')
        ->name('users.show');

    Route::post('/users', [UserManagementController::class, 'store'])
        ->middleware('permission:users.create')
        ->name('users.store');

    Route::put('/users/{user}', [UserManagementController::class, 'update'])
        ->middleware('permission:users.update')
        ->name('users.update');

    Route::patch('/users/{user}/toggle-status', [UserManagementController::class, 'toggleStatus'])
        ->middleware('permission:users.update')
        ->name('users.toggle-status');

    Route::delete('/users/{user}', [UserManagementController::class, 'destroy'])
        ->middleware('permission:users.delete')
        ->name('users.destroy');

    Route::get('/printers', [\App\Http\Controllers\PrinterController::class, 'index'])
        ->middleware('permission:users.view')
        ->name('printers.index');

    Route::get('/printers/create', [\App\Http\Controllers\PrinterController::class, 'create'])
        ->middleware('permission:users.create')
        ->name('printers.create');

    Route::post('/printers', [\App\Http\Controllers\PrinterController::class, 'store'])
        ->middleware('permission:users.create')
        ->name('printers.store');

    Route::get('/printers/{printer}/edit', [\App\Http\Controllers\PrinterController::class, 'edit'])
        ->middleware('permission:users.update')
        ->name('printers.edit');

    Route::put('/printers/{printer}', [\App\Http\Controllers\PrinterController::class, 'update'])
        ->middleware('permission:users.update')
        ->name('printers.update');

    Route::delete('/printers/{printer}', [\App\Http\Controllers\PrinterController::class, 'destroy'])
        ->middleware('permission:users.delete')
        ->name('printers.destroy');

    Route::get('/suppliers', [SupplierController::class, 'index'])
        ->middleware('permission:suppliers.view')
        ->name('suppliers.index');
    Route::get('/suppliers/pdf', [SupplierController::class, 'exportPdf'])
        ->middleware('permission:suppliers.view')
        ->name('suppliers.pdf');
    Route::get('/suppliers/create', [SupplierController::class, 'create'])
        ->middleware('permission:suppliers.create')
        ->name('suppliers.create');
    Route::post('/suppliers', [SupplierController::class, 'store'])
        ->middleware('permission:suppliers.create')
        ->name('suppliers.store');
    Route::get('/suppliers/{supplier}', [SupplierController::class, 'show'])
        ->middleware('permission:suppliers.view')
        ->name('suppliers.show');
    Route::get('/suppliers/{supplier}/edit', [SupplierController::class, 'edit'])
        ->middleware('permission:suppliers.update')
        ->name('suppliers.edit');
    Route::put('/suppliers/{supplier}', [SupplierController::class, 'update'])
        ->middleware('permission:suppliers.update')
        ->name('suppliers.update');
    Route::delete('/suppliers/{supplier}', [SupplierController::class, 'destroy'])
        ->middleware('permission:suppliers.delete')
        ->name('suppliers.destroy');
    Route::post('/suppliers/{supplier}/payments', [SupplierController::class, 'addPayment'])
        ->middleware('permission:suppliers.update')
        ->name('suppliers.payments.store');
    Route::post('/suppliers/{supplier}/returns', [SupplierController::class, 'addReturn'])
        ->middleware('permission:suppliers.update')
        ->name('suppliers.returns.store');

    Route::get('/purchases', [PurchaseController::class, 'index'])
        ->middleware('permission:purchases.view')
        ->name('purchases.index');
    Route::get('/purchases/pdf', [PurchaseController::class, 'exportPdf'])
        ->middleware('permission:purchases.view')
        ->name('purchases.pdf');
    Route::get('/purchases/create', [PurchaseController::class, 'create'])
        ->middleware('permission:purchases.create')
        ->name('purchases.create');
    Route::post('/purchases', [PurchaseController::class, 'store'])
        ->middleware('permission:purchases.create')
        ->name('purchases.store');
    Route::patch('/purchases/{purchase}/approve', [PurchaseController::class, 'approve'])
        ->middleware('permission:purchases.approve')
        ->name('purchases.approve');
    Route::patch('/purchases/{purchase}/reject', [PurchaseController::class, 'reject'])
        ->middleware('permission:purchases.approve')
        ->name('purchases.reject');
    Route::patch('/purchases/{purchase}/complete', [PurchaseController::class, 'complete'])
        ->middleware('permission:purchases.create')
        ->name('purchases.complete');
    Route::get('/purchases/{purchase}', [PurchaseController::class, 'show'])
        ->middleware('permission:purchases.view')
        ->name('purchases.show');
    Route::get('/purchases/{purchase}/invoice', [PurchaseController::class, 'invoice'])
        ->middleware('permission:purchases.view')
        ->name('purchases.invoice');
    Route::post('/purchases/{purchase}/direct-print', [PurchaseController::class, 'directPrint'])
        ->middleware('permission:purchases.view')
        ->name('purchases.direct-print');
    Route::get('/purchases/{purchase}/invoice-file', [PurchaseController::class, 'viewUploadedInvoice'])
        ->middleware('permission:purchases.view')
        ->name('purchases.invoice-file.view');
    Route::patch('/purchases/{purchase}/invoice-file', [PurchaseController::class, 'updateUploadedInvoice'])
        ->middleware('permission:purchases.update')
        ->name('purchases.invoice-file.update');
    Route::get('/purchases/{purchase}/invoice-file/download', [PurchaseController::class, 'downloadUploadedInvoice'])
        ->middleware('permission:purchases.view')
        ->name('purchases.invoice-file.download');

    Route::get('/coupons', [CouponController::class, 'index'])
        ->middleware('permission:coupons.view')
        ->name('coupons.index');
    Route::get('/coupons/create', [CouponController::class, 'create'])
        ->middleware('permission:coupons.create')
        ->name('coupons.create');
    Route::post('/coupons', [CouponController::class, 'store'])
        ->middleware('permission:coupons.create')
        ->name('coupons.store');
    Route::get('/coupons/{coupon}', [CouponController::class, 'show'])
        ->middleware('permission:coupons.view')
        ->name('coupons.show');
    Route::get('/coupons/{coupon}/edit', [CouponController::class, 'edit'])
        ->middleware('permission:coupons.update')
        ->name('coupons.edit');
    Route::put('/coupons/{coupon}', [CouponController::class, 'update'])
        ->middleware('permission:coupons.update')
        ->name('coupons.update');
    Route::delete('/coupons/{coupon}', [CouponController::class, 'destroy'])
        ->middleware('permission:coupons.delete')
        ->name('coupons.destroy');

    Route::get('/offers', [OfferController::class, 'index'])
        ->middleware('permission:offers.view')
        ->name('offers.index');
    Route::get('/offers/create', [OfferController::class, 'create'])
        ->middleware('permission:offers.create')
        ->name('offers.create');
    Route::post('/offers', [OfferController::class, 'store'])
        ->middleware('permission:offers.create')
        ->name('offers.store');
    Route::get('/offers/{offer}', [OfferController::class, 'show'])
        ->middleware('permission:offers.view')
        ->name('offers.show');
    Route::get('/offers/{offer}/edit', [OfferController::class, 'edit'])
        ->middleware('permission:offers.update')
        ->name('offers.edit');
    Route::put('/offers/{offer}', [OfferController::class, 'update'])
        ->middleware('permission:offers.update')
        ->name('offers.update');
    Route::delete('/offers/{offer}', [OfferController::class, 'destroy'])
        ->middleware('permission:offers.delete')
        ->name('offers.destroy');

    Route::get('/employees', [EmployeeController::class, 'index'])
        ->middleware('permission:employees.view')
        ->name('employees.index');
    Route::get('/employees/export/pdf', [EmployeeController::class, 'exportPdf'])
        ->middleware('permission:employees.view')
        ->name('employees.export.pdf');
    Route::get('/employees/export/excel', [EmployeeController::class, 'exportExcel'])
        ->middleware('permission:employees.view')
        ->name('employees.export.excel');
    Route::get('/employees/create', [EmployeeController::class, 'create'])
        ->middleware('permission:employees.create')
        ->name('employees.create');
    Route::post('/employees', [EmployeeController::class, 'store'])
        ->middleware('permission:employees.create')
        ->name('employees.store');
    Route::get('/employees/{employee}/financial-report/print', [EmployeeController::class, 'printFinancialMonthlyReport'])
        ->middleware('permission:employees.view')
        ->name('employees.financial-report.print');
    Route::get('/employees/{employee}/financial-report/pdf', [EmployeeController::class, 'exportFinancialMonthlyReportPdf'])
        ->middleware('permission:employees.view')
        ->name('employees.financial-report.pdf');
    Route::get('/employees/{employee}/financial-report/excel', [EmployeeController::class, 'exportFinancialMonthlyReportExcel'])
        ->middleware('permission:employees.view')
        ->name('employees.financial-report.excel');
    Route::get('/employees/{employee}', [EmployeeController::class, 'show'])
        ->middleware('permission:employees.view')
        ->name('employees.show');
    Route::get('/employees/{employee}/edit', [EmployeeController::class, 'edit'])
        ->middleware('permission:employees.update')
        ->name('employees.edit');
    Route::put('/employees/{employee}', [EmployeeController::class, 'update'])
        ->middleware('permission:employees.update')
        ->name('employees.update');
    Route::delete('/employees/{employee}', [EmployeeController::class, 'destroy'])
        ->middleware('permission:employees.delete')
        ->name('employees.destroy');
    Route::post('/employees/{employee}/deductions', [EmployeeSalaryAdjustmentController::class, 'storeDeduction'])
        ->middleware('permission:employees.update')
        ->name('employees.deductions.store');
    Route::post('/employees/{employee}/product-charges', [EmployeeSalaryAdjustmentController::class, 'storeProductCharge'])
        ->middleware('permission:employees.update')
        ->name('employees.product-charges.store');
    Route::put('/employees/{employee}/adjustments/{adjustment}', [EmployeeSalaryAdjustmentController::class, 'update'])
        ->middleware('permission:employees.update')
        ->name('employees.adjustments.update');
    Route::delete('/employees/{employee}/adjustments/{adjustment}', [EmployeeSalaryAdjustmentController::class, 'destroy'])
        ->middleware('permission:employees.update')
        ->name('employees.adjustments.destroy');
    Route::post('/employees/{employee}/delivery-settlements', [EmployeeController::class, 'settleDeliveryOrders'])
        ->middleware('permission:employees.update')
        ->name('employees.delivery-settlements.store');

    Route::get('/attendance', [AttendanceController::class, 'index'])
        ->middleware('permission:attendance.view')
        ->name('attendance.index');
    Route::get('/attendance/export/pdf', [AttendanceController::class, 'exportPdf'])
        ->middleware('permission:attendance.view')
        ->name('attendance.export.pdf');
    Route::get('/attendance/export/excel', [AttendanceController::class, 'exportExcel'])
        ->middleware('permission:attendance.view')
        ->name('attendance.export.excel');
    Route::get('/attendance/create', [AttendanceController::class, 'create'])
        ->middleware('permission:attendance.create')
        ->name('attendance.create');
    Route::post('/attendance', [AttendanceController::class, 'store'])
        ->middleware('permission:attendance.create')
        ->name('attendance.store');
    Route::get('/attendance/{attendance}/edit', [AttendanceController::class, 'edit'])
        ->middleware('permission:attendance.update')
        ->name('attendance.edit');
    Route::put('/attendance/{attendance}', [AttendanceController::class, 'update'])
        ->middleware('permission:attendance.update')
        ->name('attendance.update');
    Route::delete('/attendance/{attendance}', [AttendanceController::class, 'destroy'])
        ->middleware('permission:attendance.delete')
        ->name('attendance.destroy');
    Route::post('/attendance/quick-check-in', [AttendanceController::class, 'quickCheckIn'])
        ->middleware('permission:attendance.create')
        ->name('attendance.quick-check-in');
    Route::post('/attendance/quick-check-out', [AttendanceController::class, 'quickCheckOut'])
        ->middleware('permission:attendance.create')
        ->name('attendance.quick-check-out');

    Route::get('/salaries', [SalaryController::class, 'index'])
        ->middleware('permission:salaries.view')
        ->name('salaries.index');
    Route::get('/salaries/export/pdf', [SalaryController::class, 'exportPdf'])
        ->middleware('permission:salaries.view')
        ->name('salaries.export.pdf');
    Route::get('/salaries/export/excel', [SalaryController::class, 'exportExcel'])
        ->middleware('permission:salaries.view')
        ->name('salaries.export.excel');
    Route::get('/salaries/create', [SalaryController::class, 'create'])
        ->middleware('permission:salaries.create')
        ->name('salaries.create');
    Route::post('/salaries', [SalaryController::class, 'store'])
        ->middleware('permission:salaries.create')
        ->name('salaries.store');
    Route::get('/salaries/{salary}', [SalaryController::class, 'show'])
        ->middleware('permission:salaries.view')
        ->name('salaries.show');
    Route::patch('/salaries/{salary}/mark-paid', [SalaryController::class, 'markPaid'])
        ->middleware('permission:salaries.update')
        ->name('salaries.mark-paid');

    Route::prefix('financial')->name('financial.')->middleware('permission:financial.view')->group(function () {
        Route::get('/', [FinancialController::class, 'index'])->name('index');
        Route::get('/export/pdf', [FinancialController::class, 'exportPdf'])->name('export.pdf');
        Route::get('/export/excel', [FinancialController::class, 'exportExcel'])->name('export.excel');
    });
});
