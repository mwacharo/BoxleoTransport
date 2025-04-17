<?php

use App\Exports\PODReportExport;
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
use App\Http\Controllers\Api\LevelApiController;
use App\Http\Controllers\Api\BinApiController;
use App\Http\Controllers\Api\BayApiController;
use App\Http\Controllers\Api\RowApiController;
use App\Http\Controllers\Api\AreaApiController;
use App\Http\Controllers\Api\WarehouseApiController;
use App\Http\Controllers\Api\GeofenceApiController;
use App\Http\Controllers\Api\VehicleApiController;
use Illuminate\Http\Request;





use App\Http\Controllers\Api\ReportApiController as ApiReportApiController;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PODReportCsvExport;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Auth;

Route::post('v1/login', [AuthController::class, 'riderLogin']);

// Route::post('/test-login', function (Request $request) {
//   $credentials = $request->only('email', 'password');
//   if (Auth::attempt($credentials)) {
//       $user = Auth::user();
//       return response()->json(['user' => $user]);
//   } else {
//       return response()->json(['message' => 'Invalid credentials'], 401);
//   }
// });

 
Route::apiResource('v1/users', UsersApiController::class)->only([
  'index', 'store', 'update', 'destroy'
]);
Route::put('v1/users/{id}/toggle-status', [UsersApiController::class, 'toggleStatus']);

Route::apiResource('v1/branches', BranchesApiController::class)->only([
  'index', 'store', 'update', 'destroy'
]);


Route::apiResource('v1/calendar', CalendarApiController::class)->only([
  'index', 'store', 'update', 'destroy'
]);

Route::post('/reports/generateReport', [ApiReportApiController::class, 'generateReport']);
Route::post('v1/reports/downloadExcel', [ApiReportApiController::class, 'downloadExcel']);
Route::post('v1/reports/downloadPDF', [ApiReportApiController::class, 'downloadPDF']);




// Route::apiResource('v1/industries', IndustriesApiController::class)->only([
//   'index', 'store', 'update', 'destroy'
// ]);

Route::apiResource('v1/services', ServicesApiController::class)->only([
  'index', 'store', 'update', 'destroy'
]);
// Route::get('v1/services-with-conditions', [ServicesApiController::class, 'getServicesWithConditions']);
Route::get('v1/services-with-conditions/{id}', [ServicesApiController::class, 'getServicesWithConditions']);

Route::post('v1/save-merchant-services', [ServicesApiController::class, 'storeConditions']);

// save-merchant-services

Route::post('/services/conditions', [ServicesApiController::class, 'storeConditions']);



Route::get('v1/service-statistics',[ServicesApiController::class,'statistics']);


Route::get('v1/dealsAnnually', [DashboardApiContoller::class, 'dealsAnnually']);
// orders status by count 
Route::get('v1/orderStatusCounts', [DashboardApiContoller::class, 'orderStatusCounts']);

// Route::get('v1/dashoboad', [DashboardApiContoller::class, 'dashoboad']);
Route::get('/v1/fetchDashboardData', [DashboardApiContoller::class, 'fetchDashboardData']);
Route::get('/v1/ordersAnnually', [DashboardApiContoller::class, 'ordersAnnually']);

// ordersAnnually


Route::get('v1/statistics', [DashboardApiContoller::class, 'statistics']);

// Route::get('google-sheets/read', [GoogleSheetsController::class, 'readSheets'])->name('google.sheets.read');


// Route::resource('sheets', SheetApiController::class);
Route::apiResource('v1/sheets', SheetApiController::class);
Route::post('v1/sheets/{id}/sync', [SheetApiController::class, 'sync']);
Route::post('v1/update-sheet', [SheetApiController::class, 'updateSheet']);


Route::apiResource('/v1/vendors', VendorApiController::class);

Route::apiResource('/v1/orders', OrderApiController::class);
Route::post('/v1/orders/details', [OrderApiController::class, 'details']);
// orders assign to rider through gecoding
Route::post('/v1/orders/assignOrders', [OrderApiController::class, 'assignOrders']);

// Route::post('/v1/orders/by-ids', [OrderApiController::class, 'by-ids']);
// details of a specific order_no

Route::get('/orders/{id}', [OrderApiController::class, 'order']);
Route::post('v1/orderImport', [OrderApiController::class, 'orderImport']);

// Route::put('v1/order-products/update', [OrderApiController::class, 'updateProductDetails']);
Route::post('v1/order-products/save', [OrderApiController::class, 'saveProductDetails']);
Route::delete('v1/order-product/{id}', [OrderApiController::class, 'destroy']);
Route::post('v1/order-product', [OrderApiController::class, 'store']);
// Route::post('v1/order-pod', [OrderApiController::class, 'storePod']);
Route::post('v1/order-pod/{orderNo}', [OrderApiController::class, 'uploadPod']);
Route::get('v1/order-pod/{orderNo}', [OrderApiController::class, 'getPod']);
Route::delete('v1/order-pod/{orderNo}', [OrderApiController::class, 'destroyPod']);




Route::post('/v1/orders/bulk-delete', [OrderApiController::class, 'bulkDelete']);
Route::post('/v1/orders/bulk-assign-rider', [OrderApiController::class, 'bulkAssignRider']);
Route::post('/v1/orders/bulk-assign-driver', [OrderApiController::class, 'bulkAssignDriver']);
Route::post('/v1/orders/bulk-update-status', [OrderApiController::class, 'bulkUpdateStatus']);
Route::post('/v1/orders/bulk-categorize', [OrderApiController::class, 'bulkCategorize']);
Route::post('/v1/orders/bulk-auto-allocate', [OrderApiController::class, 'bulkAutoAllocate']);
Route::post('/v1/orders/bulk-print', [OrderApiController::class, 'bulkPrint']);

Route::post('/v1/pickOrderitems', [OrderApiController::class, 'pickOrderitem']);
Route::post('/v1/dispatchOrders', [OrderApiController::class, 'dispatchOrders']);
Route::post('/v1/returnOrders', [OrderApiController::class, 'returnOrders']);




// geocode order
Route::post('v1/geocodeAddress', [OrderApiController::class, 'geocodeAddress']);

Route::apiResource('/v1/geofences', GeofenceApiController::class);
Route::post('v1/geofences', [GeofenceApiController::class, 'store']);
Route::get('v1/geofences', [GeofenceApiController::class, 'index']);



Route::apiResource('/v1/riders', RiderApiController::class);
Route::put('riders/{id}/geofence', [RiderApiController::class, 'updateGeofence']);
// clear rider
Route::post('/v1/riders/{rider}/clear', [RiderApiController::class, 'clear']);

Route::apiResource('/v1/drivers', DriverApiContoller::class);
Route::apiResource('/v1/vehicles', VehicleApiController::class);

Route::apiResource('/v1/clients', ClientApiContoller::class);
Route::apiResource('/v1/ordercategories', OrderCategoryApiController::class);
Route::apiResource('/v1/orderstatus', OrderStatusApiController::class);
Route::get('/v1/geocode-orders', [OrderApiController::class, 'geocodeOrders']);
Route::apiResource('/v1/products', ProductApiController::class);
Route::get('v1/products/{id}/instances', [ProductApiController::class, 'getProductInstances']);

Route::apiResource('/v1/warehouses', WarehouseApiController::class);
Route::apiResource('/v1/levels', LevelApiController::class);
Route::apiResource('/v1/bins', BinApiController::class);
Route::apiResource('/v1/bays', BayApiController::class);
Route::apiResource('/v1/rows', RowApiController::class);
Route::apiResource('/v1/areas', AreaApiController::class);


// Define the routes for bulk actions
Route::post('/v1/bulkAssignBin', [ProductApiController::class, 'bulkAssignBin']);
// Route::post('/v1/bulk-transfer', [InventoryController::class, 'bulkTransfer']);
Route::post('/v1/bulk-pick', [ProductApiController::class, 'bulkPick']);
Route::post('/v1/bulk-assign-to-order', [ProductApiController::class, 'bulkAssignToOrder']);
Route::post('/v1/bulk-return', [ProductApiController::class, 'bulkReturn']);
Route::post('/v1/bulk-update-status', [ProductApiController::class, 'bulkUpdateStatus']);
Route::post('/v1/bulk-print', [ProductApiController::class, 'bulkPrint']);
Route::post('/v1/bulkDelete', [ProductApiController::class, 'bulkDelete']);



//
// // Receiving Inventory
// Route::post('/api/receive-single', [ProductApiController::class, 'receiveSingle']);
Route::post('/v1/receiveBulk', [ProductApiController::class, 'receiveBulk']);

// report

Route::get('v1/roles', [UsersApiController::class, 'roles']);
Route::get('v1/permissions', [UsersApiController::class, 'permissions']);



// Route::put('/users/{userId}/role', [ApiUserController::class, 'updateRole']);
// Route::put('/users/{userId}/permissions', [ApiUserController::class, 'updatePermissions']);


Route::get('v1/users/{id}/permissions', [UsersApiController::class, 'show']);
Route::put('v1/users/{id}/permissions', [UsersApiController::class, 'update']);

Route::get('/v1/roles/{roleId}/permissions', [UsersApiController::class, 'getRolePermissions']);
Route::put('v1/roles/{roleId}/permissions', [UsersApiController::class, 'updateRolePermissions']);



Route::post('/v1/permissions', [UsersApiController::class, 'storePermission']);

// specific permission
Route::get('/v1/permissions/{permissionId}', [UsersApiController::class, 'showPermission']);
Route::put('/v1/permissions/{permissionId}', [UsersApiController::class, 'updatePermission']);
Route::delete('/v1/permissions/{permissionId}', [UsersApiController::class, 'destroyPermission']);


//  roles
Route::post('/v1/roles', [UsersApiController::class, 'storeRole']);
// Route::get('/v1/roles/{roleId}', [UsersApiController::class, 'showRole']);
Route::put('/v1/roles/{roleId}', [UsersApiController::class, 'updateRole']);
Route::delete('/v1/roles/{roleId}', [UsersApiController::class, 'destroyRole']);


//  specific user
Route::get('/users/{userId}/permissions', [UsersApiController::class, 'showUserPermissions']);
Route::put('/users/{userId}/permissions', [UsersApiController::class, 'updateUserPermissions']);


// Show permissions for a specific role
Route::get('/roles/{roleId}/permissions', [UsersApiController::class, 'getRolePermissions']);
Route::put('/roles/{roleId}/permissions', [UsersApiController::class, 'updateRolePermissions']);


// // Storage and Binning
// Route::post('/api/assign-bin', [ProductApiController::class, 'assignBin']);
// Route::post('/api/relocate', [ProductApiController::class, 'relocate']);
// Route::post('/api/bulk-assign-bin', [ProductApiController::class, 'bulkAssignBin']);
// Route::post('/api/bulk-relocate', [ProductApiController::class, 'bulkRelocate']);
//
// // Picking and Packing
// Route::post('/api/pick', [ProductApiController::class, 'pick']);
// Route::post('/api/pack', [ProductApiController::class, 'pack']);
// Route::post('/api/bulk-pick', [ProductApiController::class, 'bulkPick']);
// Route::post('/api/bulk-pack', [ProductApiController::class, 'bulkPack']);
//
// // Inventory Management
// Route::post('/api/adjust-quantity', [InventoryController::class, 'adjustQuantity']);
// Route::get('/api/check-availability', [InventoryController::class, 'checkAvailability']);
// Route::post('/api/bulk-adjust-quantity', [InventoryController::class, 'bulkAdjustQuantity']);
// Route::get('/api/bulk-check-availability', [InventoryController::class, 'bulkCheckAvailability']);
//
// // Stock Transfers
// Route::post('/api/transfer', [InventoryController::class, 'transfer']);
// Route::post('/api/bulk-transfer', [InventoryController::class, 'bulkTransfer']);
//
// // Order Management
// Route::post('/api/create-order', [OrderController::class, 'createOrder']);
// Route::post('/api/update-order-status', [OrderController::class, 'updateOrderStatus']);
// Route::post('/api/bulk-create-orders', [OrderController::class, 'bulkCreateOrders']);
// Route::post('/api/bulk-update-order-statuses', [OrderController::class, 'bulkUpdateOrderStatuses']);
//
// // Returns and Restocking
// Route::post('/api/process-return', [InventoryController::class, 'processReturn']);
// Route::post('/api/bulk-process-returns', [InventoryController::class, 'bulkProcessReturns']);
//
// // Cycle Counting and Auditing
// Route::post('/api/perform-cycle-count', [InventoryController::class, 'performCycleCount']);
// Route::post('/api/audit-inventory', [InventoryController::class, 'auditInventory']);
// Route::post('/api/bulk-perform-cycle-counts', [InventoryController::class, 'bulkPerformCycleCounts']);
// Route::post('/api/bulk-audit-inventory', [InventoryController::class, 'bulkAuditInventory']);
//
// // Reporting and Analytics
// Route::get('/api/generate-product-report', [InventoryController::class, 'generateProductReport']);
// Route::get('/api/track-product-movement', [InventoryController::class, 'trackProductMovement']);
// Route::get('/api/generate-bulk-product-reports', [InventoryController::class, 'generateBulkProductReports']);
// Route::get('/api/track-bulk-product-movement', [InventoryController::class, 'trackBulkProductMovement']);
