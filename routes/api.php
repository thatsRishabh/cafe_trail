<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserInfo;
use App\Http\Controllers\MobileController;
use App\Http\Controllers\WebController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/checkconnection', [UserInfo::class, 'checkConnection']);
Route::post('temp', [App\Http\Controllers\UserInfo::class, 'temp']); 
Route::post('temp1', [App\Http\Controllers\UserInfo::class, 'temp1']); 

// User login
Route::post('user-login', [App\Http\Controllers\UserLoginController::class, 'login']);

//  // change-password
// Route::post('change-password', [App\Http\Controllers\UserLoginController::class, 'changePassword']);
Route::middleware('auth:api')->group(function () {
    // User logout 
    Route::post('user-logout', [App\Http\Controllers\UserLoginController::class, 'logout']);

    // change-password
    Route::post('change-password', [App\Http\Controllers\UserLoginController::class, 'changePassword']);

    // forget password
    Route::post('forget-password', [App\Http\Controllers\UserLoginController::class, 'forgetPassword']);

    // Order
    Route::post('orders', [App\Http\Controllers\OrderController::class, 'searchOrder']); 
    Route::resource('order', App\Http\Controllers\OrderController::class)->only(['store','destroy','show', 'update']);
    Route::get('print-order/{id?}', [App\Http\Controllers\OrderController::class, 'printOrder']); 
  
    // dashboard
    Route::post('dashboard', [App\Http\Controllers\DashboardController::class, 'dashboard']);
    Route::post('category-wise-list', [App\Http\Controllers\DashboardController::class, 'orderList']); 
    Route::post('dashboard-graph', [App\Http\Controllers\DashboardController::class, 'dashboardGraph']); 
    Route::post('dashboard-graph-list', [App\Http\Controllers\DashboardController::class, 'dashboardGraphByName']); 

    // product-menu
        Route::post('product-menus', [App\Http\Controllers\ProductMenuController::class, 'searchProductMenu']); 
        Route::resource('product-menu', App\Http\Controllers\ProductMenuController::class)->only(['store','destroy','show']);
        Route::post('product-menu-update/{id?}', [App\Http\Controllers\ProductMenuController::class, 'update']); 
        Route::post('product-menus-list', [App\Http\Controllers\ProductMenuController::class, 'productMenuList']);

        // product-info
        Route::post('product-infos', [App\Http\Controllers\ProductInfoController::class, 'searchProductInfo']); 
        Route::resource('product-info', App\Http\Controllers\ProductInfoController::class)->only(['store','destroy','show', 'update']);

        // ProductStockManage
        Route::post('product-stock-manages', [App\Http\Controllers\ProductStockManageController::class, 'searchProductStockManage']); 
        Route::resource('product-stock-manage', App\Http\Controllers\ProductStockManageController::class)->only(['store','destroy','show', 'update']);


        // Unit
        Route::post('units', [App\Http\Controllers\UnitController::class, 'searchUnit']); 
        Route::resource('unit', App\Http\Controllers\UnitController::class)->only(['store','destroy','show', 'update']);

        // Category
        Route::post('categorys', [App\Http\Controllers\CategoryController::class, 'searchCategory']); 
        Route::resource('category', App\Http\Controllers\CategoryController::class)->only(['store','destroy','show']);
        Route::post('subcategorys', [App\Http\Controllers\CategoryController::class, 'searchSubcategory']); 
        Route::post('category-update/{id?}', [App\Http\Controllers\CategoryController::class, 'update']); 

        // Employee
        Route::post('employees', [App\Http\Controllers\EmployeeController::class, 'searchEmployee']); 
        Route::resource('employee', App\Http\Controllers\EmployeeController::class)->only(['store','destroy','show']);
        Route::post('employee-update/{id?}', [App\Http\Controllers\EmployeeController::class, 'update']); 

        // SalaryManagement
        Route::post('salary-managements', [App\Http\Controllers\SalaryManagementController::class, 'searchSalary']); 
        Route::resource('salary-management', App\Http\Controllers\SalaryManagementController::class)->only(['store','destroy','show', 'update']);

        // EmployeeAttendence
        Route::post('employee-attendences', [App\Http\Controllers\EmployeeAttendenceController::class, 'searchEmployeeAttendence']); 
        Route::resource('employee-attendence', App\Http\Controllers\EmployeeAttendenceController::class)->only(['store','destroy','show', 'update']);
        Route::post('attendences-date-wise', [App\Http\Controllers\EmployeeAttendenceController::class, 'dateWiseSearch']); 
        Route::post('monthly-attendence', [App\Http\Controllers\EmployeeAttendenceController::class, 'monthlyAttendence']); 
        Route::get('employee-id', [App\Http\Controllers\EmployeeAttendenceController::class, 'employeeID']); 

        // customer
        Route::post('customers', [App\Http\Controllers\CustomerController::class, 'searchCustomer']); 
        Route::resource('customer', App\Http\Controllers\CustomerController::class)->only(['store','destroy','show', 'update']);

        // Route::post('joindata', [App\Http\Controllers\ProductInfoController::class, 'joinData']); 

        // expense
        Route::post('expenses', [App\Http\Controllers\ExpenseController::class, 'searchExpense']); 
        Route::resource('expense', App\Http\Controllers\ExpenseController::class)->only(['store','destroy','show', 'update']);

        // recipe
        Route::post('recipes', [App\Http\Controllers\RecipeController::class, 'searchRecipe']); 
        Route::resource('recipe', App\Http\Controllers\RecipeController::class)->only(['store','destroy','show', 'update']);

        // CustomerAccountManage
        Route::post('customer-account-manages', [App\Http\Controllers\CustomerAccountManageController::class, 'searchCustomerAccount']); 
        Route::resource('customer-account-manage', App\Http\Controllers\CustomerAccountManageController::class)->only(['store','destroy','show', 'update']);
            
});

// // dashboard
// Route::post('dashboard', [App\Http\Controllers\DashboardController::class, 'dashboard']);
// Route::post('category-wise-list', [App\Http\Controllers\DashboardController::class, 'orderList']); 
// Route::post('dashboard-graph', [App\Http\Controllers\DashboardController::class, 'dashboardGraph']); 
// Route::post('dashboard-graph-list', [App\Http\Controllers\DashboardController::class, 'dashboardGraphByName']); 

// // product-menu
// Route::post('product-menus', [App\Http\Controllers\ProductMenuController::class, 'searchProductMenu']); 
// Route::resource('product-menu', App\Http\Controllers\ProductMenuController::class)->only(['store','destroy','show']);
// Route::post('product-menu-update/{id?}', [App\Http\Controllers\ProductMenuController::class, 'update']); 
// Route::post('product-menus-list', [App\Http\Controllers\ProductMenuController::class, 'productMenuList']);

// // product-info
// Route::post('product-infos', [App\Http\Controllers\ProductInfoController::class, 'searchProductInfo']); 
// Route::resource('product-info', App\Http\Controllers\ProductInfoController::class)->only(['store','destroy','show', 'update']);

// // ProductStockManage
// Route::post('product-stock-manages', [App\Http\Controllers\ProductStockManageController::class, 'searchProductStockManage']); 
// Route::resource('product-stock-manage', App\Http\Controllers\ProductStockManageController::class)->only(['store','destroy','show', 'update']);


// // Unit
// Route::post('units', [App\Http\Controllers\UnitController::class, 'searchUnit']); 
// Route::resource('unit', App\Http\Controllers\UnitController::class)->only(['store','destroy','show', 'update']);

// // Category
// Route::post('categorys', [App\Http\Controllers\CategoryController::class, 'searchCategory']); 
// Route::resource('category', App\Http\Controllers\CategoryController::class)->only(['store','destroy','show']);
// Route::post('subcategorys', [App\Http\Controllers\CategoryController::class, 'searchSubcategory']); 
// Route::post('category-update/{id?}', [App\Http\Controllers\CategoryController::class, 'update']); 

// // Employee
// Route::post('employees', [App\Http\Controllers\EmployeeController::class, 'searchEmployee']); 
// Route::resource('employee', App\Http\Controllers\EmployeeController::class)->only(['store','destroy','show', 'update']);


// // EmployeeAttendence
// Route::post('employee-attendences', [App\Http\Controllers\EmployeeAttendenceController::class, 'searchEmployeeAttendence']); 
// Route::resource('employee-attendence', App\Http\Controllers\EmployeeAttendenceController::class)->only(['store','destroy','show', 'update']);
// Route::post('attendences-date-wise', [App\Http\Controllers\EmployeeAttendenceController::class, 'dateWiseSearch']); 
// Route::post('monthly-attendence', [App\Http\Controllers\EmployeeAttendenceController::class, 'monthlyAttendence']); 

// // customer
// Route::post('customers', [App\Http\Controllers\CustomerController::class, 'searchCustomer']); 
// Route::resource('customer', App\Http\Controllers\CustomerController::class)->only(['store','destroy','show', 'update']);


// // expense
// Route::post('expenses', [App\Http\Controllers\ExpenseController::class, 'searchExpense']); 
// Route::resource('expense', App\Http\Controllers\ExpenseController::class)->only(['store','destroy','show', 'update']);

// // recipe
// Route::post('recipes', [App\Http\Controllers\RecipeController::class, 'searchRecipe']); 
// Route::resource('recipe', App\Http\Controllers\RecipeController::class)->only(['store','destroy','show', 'update']);

// Order
// Route::post('orders', [App\Http\Controllers\OrderController::class, 'searchOrder']); 
// Route::resource('order', App\Http\Controllers\OrderController::class)->only(['store','destroy','show', 'update']);
// Route::get('print-order/{id?}', [App\Http\Controllers\OrderController::class, 'printOrder']); 
// Route::post('orders-decution', [App\Http\Controllers\OrderController::class, 'orderDecution']); 

// // CustomerAccountManage
// Route::post('customer-account-manages', [App\Http\Controllers\CustomerAccountManageController::class, 'searchCustomerAccount']); 
// Route::resource('customer-account-manage', App\Http\Controllers\CustomerAccountManageController::class)->only(['store','destroy','show', 'update']);