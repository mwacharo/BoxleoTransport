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
Route::apiResource('/v1/riders', RiderApiContoller::class);
Route::apiResource('/v1/drivers', DriverApiContoller::class);
Route::apiResource('/v1/clients', ClientApiContoller::class);
Route::apiResource('/v1/ordercategories', OrderCategoryApiController::class);
Route::apiResource('/v1/orderstatus', OrderStatusApiController::class);
Route::get('/v1/geocode-orders', [OrderApiController::class, 'geocodeOrders']);
Route::apiResource('/v1/products', ProductApiController::class);
Route::get('v1/products/{id}/instances', [ProductApiController::class, 'getProductInstances']);














