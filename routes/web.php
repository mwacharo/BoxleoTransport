<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DealController;
use App\Http\Controllers\DriverController;
use App\Http\Controllers\MarketController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RiderController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VendorController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/orders', [OrderController::class, 'index'])->name('orders');
    Route::get('/drivers', [DriverController::class, 'index'])->name('drivers');
    Route::get('/riders', [RiderController::class, 'index'])->name('riders');
    Route::get('/vendors', [VendorController::class, 'index'])->name('vendors');
    Route::get('/market', [MarketController::class, 'index'])->name('market');


    Route::get('/reports', [ReportController::class, 'index'])->name('reports');
    Route::get('/users', [UserController::class, 'index'])->name('users');
    Route::get('/user-roles', [UserController::class, 'userRoles'])->name('user.roles');

    Route::get('/account', [UserController::class, 'userAcount'])->name('account');
    Route::get('/account/settings', [UserController::class, 'acountSettings'])->name('accountSettings');
});
Auth::routes();