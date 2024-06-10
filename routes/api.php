<?php

use App\Models\Branch;
use App\Models\OrderCategory;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReportApiController;
use App\Http\Controllers\Api\RiderApiContoller;
use App\Http\Controllers\Api\ClientApiContoller;
use App\Http\Controllers\Api\DealsApiController;
use App\Http\Controllers\Api\DriverApiContoller;
use App\Http\Controllers\Api\OrderApiController;
use App\Http\Controllers\Api\RiderApiController;
use App\Http\Controllers\Api\SheetApiController;
use App\Http\Controllers\Api\TasksApiController;
use App\Http\Controllers\Api\UsersApiController;
use App\Http\Controllers\Api\VendorApiController;
use App\Http\Controllers\Api\ProductApiController;
use App\Http\Controllers\Api\BranchesApiController;
use App\Http\Controllers\Api\CalendarApiController;
use App\Http\Controllers\Api\DashboardApiContoller;

use App\Http\Controllers\Api\ServicesApiController;
use App\Http\Controllers\Api\OrderStatusApiController;
use App\Http\Controllers\Api\OrderCategoryApiController;
use App\Http\Controllers\Api\ReportApiController as ApiReportApiController;

Route::apiResource('v1/users', UsersApiController::class)->only([
  'index', 'store', 'update', 'destroy'
]);
Route::put('v1/users/{id}/toggle-status', [UsersApiController::class, 'toggleStatus']);

Route::apiResource('v1/branches', BranchesApiController::class)->only([
  'index', 'store', 'update', 'destroy'
]);

Route::apiResource('v1/tasks', TasksApiController::class)->only([
  'index', 'store', 'update', 'destroy'
]);

Route::apiResource('v1/calendar', CalendarApiController::class)->only([
  'index', 'store', 'update', 'destroy'
]);

Route::apiResource('v1/deals', DealsApiController::class)->only([
  'index', 'store', 'update', 'destroy'
]);

Route::put('v1/deals/{deal}/update-status', [DealsApiController::class, 'updateStatus'])
->name('deals.updateStatus');
// routes/api.php
Route::post('/deals/search', [DealsApiController::class, 'search']);
Route::post('/upload', [DealsApiController::class, 'upload']);
Route::post('/reports/generate', [ApiReportApiController::class, 'generate']);


// Route::apiResource('v1/industries', IndustriesApiController::class)->only([
//   'index', 'store', 'update', 'destroy'
// ]);

Route::apiResource('v1/services', ServicesApiController::class)->only([
  'index', 'store', 'update', 'destroy'
]);

Route::get('v1/service-statistics',[ServicesApiController::class,'statistics']);


Route::get('v1/dealsAnnually', [DashboardApiContoller::class, 'dealsAnnually']);
Route::get('v1/dealStatusCounts', [DashboardApiContoller::class, 'dealStatusCounts']);


Route::get('v1/statistics', [DashboardApiContoller::class, 'statistics']);

// Route::get('google-sheets/read', [GoogleSheetsController::class, 'readSheets'])->name('google.sheets.read');


// Route::resource('sheets', SheetApiController::class);
Route::apiResource('v1/sheets', SheetApiController::class);
Route::post('v1/sheets/{id}/sync', [SheetApiController::class, 'sync']);

Route::apiResource('/v1/vendors', VendorApiController::class);

Route::apiResource('/v1/orders', OrderApiController::class);

Route::post('/v1/orders/bulk-delete', [OrderApiController::class, 'bulkDelete']);
Route::post('/v1/orders/bulk-assign-rider', [OrderApiController::class, 'bulkAssignRider']);
Route::post('/v1/orders/bulk-assign-driver', [OrderApiController::class, 'bulkAssignDriver']);
Route::post('/v1/orders/bulk-update-status', [OrderApiController::class, 'bulkUpdateStatus']);
Route::post('/v1/orders/bulk-categorize', [OrderApiController::class, 'bulkCategorize']);
Route::post('/v1/orders/bulk-auto-allocate', [OrderApiController::class, 'bulkAutoAllocate']);
Route::post('/v1/orders/bulk-print', [OrderApiController::class, 'bulkPrint']);


Route::apiResource('/v1/riders', RiderApiContoller::class);
Route::apiResource('/v1/drivers', DriverApiContoller::class);
Route::apiResource('/v1/clients', ClientApiContoller::class);
Route::apiResource('/v1/ordercategories', OrderCategoryApiController::class);
Route::apiResource('/v1/orderstatus', OrderStatusApiController::class);
Route::get('/v1/geocode-orders', [OrderApiController::class, 'geocodeOrders']);
Route::apiResource('/v1/products', ProductApiController::class);
Route::get('v1/products/{id}/instances', [ProductApiController::class, 'getProductInstances']);







// routes/web.php

// use App\Http\Controllers\ProductInstanceController;
// use App\Http\Controllers\OrderController;
// use App\Http\Controllers\InventoryController;

// Receiving Inventory
Route::post('/api/receive-single', [ProductApiController::class, 'receiveSingle']);
Route::post('/api/receive-bulk', [ProductApiController::class, 'receiveBulk']);

// Storage and Binning
Route::post('/api/assign-bin', [ProductApiController::class, 'assignBin']);
Route::post('/api/relocate', [ProductApiController::class, 'relocate']);
Route::post('/api/bulk-assign-bin', [ProductApiController::class, 'bulkAssignBin']);
Route::post('/api/bulk-relocate', [ProductApiController::class, 'bulkRelocate']);

// Picking and Packing
Route::post('/api/pick', [ProductApiController::class, 'pick']);
Route::post('/api/pack', [ProductApiController::class, 'pack']);
Route::post('/api/bulk-pick', [ProductApiController::class, 'bulkPick']);
Route::post('/api/bulk-pack', [ProductApiController::class, 'bulkPack']);

// Inventory Management
Route::post('/api/adjust-quantity', [InventoryController::class, 'adjustQuantity']);
Route::get('/api/check-availability', [InventoryController::class, 'checkAvailability']);
Route::post('/api/bulk-adjust-quantity', [InventoryController::class, 'bulkAdjustQuantity']);
Route::get('/api/bulk-check-availability', [InventoryController::class, 'bulkCheckAvailability']);

// Stock Transfers
Route::post('/api/transfer', [InventoryController::class, 'transfer']);
Route::post('/api/bulk-transfer', [InventoryController::class, 'bulkTransfer']);

// Order Management
Route::post('/api/create-order', [OrderController::class, 'createOrder']);
Route::post('/api/update-order-status', [OrderController::class, 'updateOrderStatus']);
Route::post('/api/bulk-create-orders', [OrderController::class, 'bulkCreateOrders']);
Route::post('/api/bulk-update-order-statuses', [OrderController::class, 'bulkUpdateOrderStatuses']);

// Returns and Restocking
Route::post('/api/process-return', [InventoryController::class, 'processReturn']);
Route::post('/api/bulk-process-returns', [InventoryController::class, 'bulkProcessReturns']);

// Cycle Counting and Auditing
Route::post('/api/perform-cycle-count', [InventoryController::class, 'performCycleCount']);
Route::post('/api/audit-inventory', [InventoryController::class, 'auditInventory']);
Route::post('/api/bulk-perform-cycle-counts', [InventoryController::class, 'bulkPerformCycleCounts']);
Route::post('/api/bulk-audit-inventory', [InventoryController::class, 'bulkAuditInventory']);

// Reporting and Analytics
Route::get('/api/generate-product-report', [InventoryController::class, 'generateProductReport']);
Route::get('/api/track-product-movement', [InventoryController::class, 'trackProductMovement']);
Route::get('/api/generate-bulk-product-reports', [InventoryController::class, 'generateBulkProductReports']);
Route::get('/api/track-bulk-product-movement', [InventoryController::class, 'trackBulkProductMovement']);















