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
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LeaveController;
use App\Http\Controllers\SalesOrderController;
use App\Http\Middleware\OnlyAdmin;

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

Route::middleware(['auth:sanctum', 'OnlyAdmin:admin'])->group(function(){
  // Calendar
  Route::get('/calendar', [DashboardController::class, 'calendar']);
  Route::get('/dashboard', [DashboardController::class, 'dashboard']);

  // User
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
  Route::delete('/employee/destroy/{id}',[EmployeeController::class,'employeeDestroy'])->name('destroy.employee');

  // Category
  Route::post('/category/store', [CategoryController::class, 'storeCategory'])->name('category.store');
  Route::post('/category/update/{id}', [CategoryController::class, 'categoryUpdate'])->name('update.category');
  Route::delete('/category/destroy/{id}',[CategoryController::class,'categoryDestroy'])->name('destroy.category');

  // Holiday
  Route::post('/holiday/insert',[HolidayController::class,'holidayInsert'])->name('insert.holiday');
  Route::post('/holiday/update/{id}', [HolidayController::class, 'holidayUpdate'])->name('update.holiday');
  Route::delete('/holiday/destroy/{id}',[HolidayController::class,'holidayDestroy'])->name('destroy.holiday');

  // Stock
  Route::post('/stock/store', [StockController::class, 'stockStore'])->name('stock.store');
  Route::post('/stock/update/{id}', [StockController::class, 'stockUpdate'])->name('stock.update');
  Route::delete('/stock/delete/{id}', [StockController::class, 'stockDestroy'])->name('stock.destroy');

  // Book
  Route::post('/book/insert',[BookController::class,'bookInsert'])->name('insert.book');
  Route::post('/book/update/{id}', [BookController::class, 'bookUpdate'])->name('update.book');
  Route::delete('/book/destroy/{id}',[BookController::class,'bookDestroy'])->name('destroy.book');
});

Route::middleware('auth:sanctum')->group(function(){
  Route::get('/holiday', [HolidayController::class, 'Holiday'])->name('holiday');

  // Category
  Route::get('/category', [CategoryController::class, 'category'])->name('category');

  // Stall
  Route::get('/store', [StallController::class, 'stall'])->name('stall');
  Route::post('/store/store', [StallController::class, 'storeStall'])->name('stall.store');
  Route::post('/store/update/{id}', [StallController::class, 'StallUpdate'])->name('update.stall');
  Route::delete('/store/destroy/{id}',[StallController::class,'StallDestroy'])->name('destroy.stall');

  //Stock
  Route::get('/stock', [StockController::class, 'stock'])->name('stock');

  //Scrap
  Route::get('/scrap', [ScrapController::class, 'scrap'])->name('scrap');
  Route::get('/scrap/customers', [ScrapController::class, 'getScrapCustomer']);
  Route::post('/scrap/insert',[ScrapController::class,'scrapInsert'])->name('insert.scrap');
  Route::post('/scrap/update/{id}', [ScrapController::class, 'scrapUpdate'])->name('update.scrap');
  Route::delete('/scrap/destroy/{id}',[ScrapController::class,'scrapDestroy'])->name('destroy.scrap');

  //Payment
  Route::get('/payment', [PaymentController::class, 'payment'])->name('payment');
  Route::post('/payment/insert',[PaymentController::class,'paymentInsert'])->name('insert.payment');
  Route::post('/payment/update/{id}', [PaymentController::class, 'paymentUpdate'])->name('update.payment');
  Route::delete('/payment/destroy/{id}',[PaymentController::class,'paymentDestroy'])->name('destroy.payment');

  //Book
  Route::get('/book', [BookController::class, 'book'])->name('book');

  //Sales Order
  Route::get('/salesorder', [SalesOrderController::class, 'salesorder'])->name('salesorder');
  Route::post('/salesorder/insert',[SalesOrderController::class,'salesorderInsert'])->name('insert.salesorder');
  Route::post('/salesorder/update/{id}', [SalesOrderController::class, 'salesorderUpdate'])->name('update.salesorder');
  Route::delete('/salesorder/destroy/{id}',[SalesOrderController::class,'salesorderDestroy'])->name('destroy.salesorder');

  // Leave
  Route::get('/leave', [LeaveController::class, 'leave']);
  Route::post('/leave/create', [LeaveController::class, 'leaveInsert']);
  Route::post('/leave/update/{id}', [LeaveController::class, 'leaveUpdate']);
  Route::get('/leave/destroy/{id}',[LeaveController::class,'leaveDestroy']);
  Route::delete('/leave/update-status', [LeaveController::class,'updateStatus']);
});

// Login
Route::post('/login', [UserController::class, 'login']);