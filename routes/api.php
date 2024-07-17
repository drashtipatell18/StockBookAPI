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
  Route::post('/user/update', [UserController::class, 'userUpdate'])->name('update.user');
  Route::get('/user/destroy',[UserController::class,'userDestroy'])->name('destroy.user');

  //Role
  Route::get('/role', [RoleController::class, 'role'])->name('role');
  Route::post('/role/store', [RoleController::class, 'roleStore'])->name('role.store');
  Route::post('/role/update', [RoleController::class, 'roleUpdate'])->name('role.update');
  Route::post('/role/delete', [RoleController::class, 'roleDestroy'])->name('role.destroy');

  Route::get('/employee', [EmployeeController::class, 'employees'])->name('employee');
  Route::post('/employee/insert',[EmployeeController::class,'employeeInsert'])->name('insert.employee');
  Route::post('/employee/update', [EmployeeController::class, 'employeeUpdate'])->name('update.employee');
  Route::post('/employee/destroy',[EmployeeController::class,'employeeDestroy'])->name('destroy.employee');

  // Category
  Route::post('/category/store', [CategoryController::class, 'storeCategory'])->name('category.store');
  Route::post('/category/update', [CategoryController::class, 'categoryUpdate'])->name('update.category');
  Route::post('/category/destroy',[CategoryController::class,'categoryDestroy'])->name('destroy.category');

  // Holiday
  Route::post('/holiday/insert',[HolidayController::class,'holidayInsert'])->name('insert.holiday');
  Route::post('/holiday/update', [HolidayController::class, 'holidayUpdate'])->name('update.holiday');
  Route::post('/holiday/destroy',[HolidayController::class,'holidayDestroy'])->name('destroy.holiday');

  // Stock
  Route::post('/stock/store', [StockController::class, 'stockStore'])->name('stock.store');
  Route::post('/stock/update', [StockController::class, 'stockUpdate'])->name('stock.update');
  Route::post('/stock/delete', [StockController::class, 'stockDestroy'])->name('stock.destroy');

  // Book
  Route::post('/book/insert',[BookController::class,'bookInsert'])->name('insert.book');
  Route::post('/book/update', [BookController::class, 'bookUpdate'])->name('update.book');
  Route::post('/book/destroy',[BookController::class,'bookDestroy'])->name('destroy.book');
});

Route::middleware('auth:sanctum')->group(function(){
  Route::get('/holiday', [HolidayController::class, 'Holiday'])->name('holiday');

  // Category
  Route::get('/category', [CategoryController::class, 'category'])->name('category');

  // Stall
  Route::get('/store', [StallController::class, 'stall'])->name('stall');
  Route::post('/store/store', [StallController::class, 'storeStall'])->name('stall.store');
  Route::post('/store/update', [StallController::class, 'StallUpdate'])->name('update.stall');
  Route::post('/store/destroy',[StallController::class,'StallDestroy'])->name('destroy.stall');

  //Stock
  Route::get('/stock', [StockController::class, 'stock'])->name('stock');

  //Scrap
  Route::get('/scrap', [ScrapController::class, 'scrap'])->name('scrap');
  Route::get('/scrap/customers', [ScrapController::class, 'getScrapCustomer']);
  Route::post('/scrap/insert',[ScrapController::class,'scrapInsert'])->name('insert.scrap');
  Route::post('/scrap/update', [ScrapController::class, 'scrapUpdate'])->name('update.scrap');
  Route::post('/scrap/destroy',[ScrapController::class,'scrapDestroy'])->name('destroy.scrap');

  //Payment
  Route::get('/payment', [PaymentController::class, 'payment'])->name('payment');
  Route::post('/payment/insert',[PaymentController::class,'paymentInsert'])->name('insert.payment');
  Route::post('/payment/update', [PaymentController::class, 'paymentUpdate'])->name('update.payment');
  Route::post('/payment/destroy',[PaymentController::class,'paymentDestroy'])->name('destroy.payment');

  //Book
  Route::get('/book', [BookController::class, 'book'])->name('book');

  //Sales Order
  Route::get('/salesorder', [SalesOrderController::class, 'salesorder'])->name('salesorder');
  Route::post('/salesorder/insert',[SalesOrderController::class,'salesorderInsert'])->name('insert.salesorder');
  Route::post('/salesorder/update', [SalesOrderController::class, 'salesorderUpdate'])->name('update.salesorder');
  Route::post('/salesorder/destroy',[SalesOrderController::class,'salesorderDestroy'])->name('destroy.salesorder');

  // Leave
  Route::get('/leave', [LeaveController::class, 'leave']);
  Route::post('/leave/create', [LeaveController::class, 'leaveInsert']);
  Route::post('/leave/update', [LeaveController::class, 'leaveUpdate']);
  Route::get('/leave/destroy',[LeaveController::class,'leaveDestroy']);
  Route::post('/leave/update-status', [LeaveController::class,'updateStatus']);
});

// Login
Route::post('/login', [UserController::class, 'login']);