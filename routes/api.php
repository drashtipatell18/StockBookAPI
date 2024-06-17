<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\HolidayController;

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