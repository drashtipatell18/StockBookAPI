<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\HolidayController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\StallController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\ScrapController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/user', [UserController::class, 'users'])->name('user');
Route::post('/user/insert',[UserController::class,'userInsert'])->name('insert.user');
Route::post('/user/update/{id}', [UserController::class, 'userUpdate'])->name('update.user');
Route::get('/user/destroy/{id}',[UserController::class,'userDestroy'])->name('destroy.user');

//Role
Route::get('/role', [RoleController::class, 'role'])->name('role');
Route::post('/role/store', [RoleController::class, 'roleStore'])->name('role.store');
Route::post('/role/update/{id}', [RoleController::class, 'roleUpdate'])->name('role.update');
Route::delete('/role/delete/{id}', [RoleController::class, 'roleDestroy'])->name('role.destroy');

Route::get('/employee', [EmployeeController::class, 'employees'])->name('employee');
Route::post('/employee/insert',[EmployeeController::class,'employeeInsert'])->name('insert.employee');
Route::post('/employee/update/{id}', [EmployeeController::class, 'employeeUpdate'])->name('update.employee');
Route::get('/employee/destroy/{id}',[EmployeeController::class,'employeeDestroy'])->name('destroy.employee');

Route::get('/holiday', [HolidayController::class, 'Holiday'])->name('holiday');
Route::post('/holiday/insert',[HolidayController::class,'holidayInsert'])->name('insert.holiday');
Route::post('/holiday/update/{id}', [HolidayController::class, 'holidayUpdate'])->name('update.holiday');
Route::get('/holiday/destroy/{id}',[HolidayController::class,'holidayDestroy'])->name('destroy.holiday');

// Category

Route::get('/category', [CategoryController::class, 'category'])->name('category');
Route::post('/category/store', [CategoryController::class, 'storeCategory'])->name('category.store');
Route::post('/category/update/{id}', [CategoryController::class, 'categoryUpdate'])->name('update.category');
Route::get('/category/destroy/{id}',[CategoryController::class,'categoryDestroy'])->name('destroy.category');

// Stall

Route::get('/stall', [StallController::class, 'stall'])->name('stall');
Route::post('/stall/store', [StallController::class, 'storeStall'])->name('stall.store');
Route::post('/stall/update/{id}', [StallController::class, 'StallUpdate'])->name('update.stall');
Route::get('/stall/destroy/{id}',[StallController::class,'StallDestroy'])->name('destroy.stall');

//Stock
Route::get('/stock', [StockController::class, 'stock'])->name('stock');
Route::post('/stock/store', [StockController::class, 'stockStore'])->name('stock.store');
Route::post('/stock/update/{id}', [StockController::class, 'stockUpdate'])->name('stock.update');
Route::delete('/stock/delete/{id}', [StockController::class, 'stockDestroy'])->name('stock.destroy');

//Scrap

Route::get('/scrap', [ScrapController::class, 'scrap'])->name('scrap');
Route::post('/scrap/insert',[ScrapController::class,'scrapInsert'])->name('insert.scrap');
Route::post('/scrap/update/{id}', [ScrapController::class, 'scrapUpdate'])->name('update.scrap');
Route::get('/scrap/destroy/{id}',[ScrapController::class,'scrapDestroy'])->name('destroy.scrap');