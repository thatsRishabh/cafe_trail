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
// Route::post('/adddata', [UserInfo::class, 'addData']);
// Route::post('/displaydata/{id?}', [UserInfo::class, 'displayData']);
// Route::post('/display-testkit-data/', [UserInfo::class, 'testKits']);
// // Route::post('/store-testkit-data/', [UserInfo::class, 'store']);
// Route::post('/store-testkit-data/', [App\Http\Controllers\UserInfo::class, 'store']);
// Route::apiResource('test-kit', App\Http\Controllers\UserInfo::class)->only(['store','destroy','show', 'update']);
// Route::post('/show-testkit/{id?}', [UserInfo::class, 'show']);

// Route::post('/add-resturant-data', [MobileController::class, 'addData']);
// Route::post('/display-resturant-data/{id?}', [MobileController::class, 'displayData']);

// Route::post('/add-product-menu', [WebController::class, 'addFood']);
// Route::post('/display-product-menu/{id?}', [WebController::class, 'displayFood']);
// Route::put('/update-product-menu', [WebController::class, 'updateFood']);
// Route::delete('/delete-product-menu/{id}', [WebController::class, 'deleteFood']);

// Route::post('/add-food', [WebController::class, 'addRecipe']);
// Route::post('/display-food/{id?}', [WebController::class, 'displayRecipe']);
// Route::put('/update-food', [WebController::class, 'updateRecipe']);
// Route::delete('/delete-food/{id}', [WebController::class, 'deleteRecipe']);


// Route::post('/add-unit', [WebController::class, 'addUnit']);
// Route::post('/display-unit/{id?}', [WebController::class, 'displayUnit']);
// Route::put('/update-unit', [WebController::class, 'updateUnit']);
// Route::delete('/delete-unit/{id}', [WebController::class, 'deleteUnit']);

// Route::post('/add-category', [WebController::class, 'addCategory']);
// Route::post('/display-category/{id?}', [WebController::class, 'displayCategory']);
// Route::put('/update-category', [WebController::class, 'updateCategory']);
// Route::delete('/delete-category/{id}', [WebController::class, 'deleteCategory']);


// product-menu
Route::post('product-menus', [App\Http\Controllers\ProductMenuController::class, 'searchProductMenu']); 
Route::resource('product-menu', App\Http\Controllers\ProductMenuController::class)->only(['store','destroy','show', 'update']);

// product-info
Route::post('product-infos', [App\Http\Controllers\ProductInfoController::class, 'searchProductInfo']); 
Route::resource('product-info', App\Http\Controllers\ProductInfoController::class)->only(['store','destroy','show', 'update']);

// Unit
Route::post('units', [App\Http\Controllers\UnitController::class, 'searchUnit']); 
Route::resource('unit', App\Http\Controllers\UnitController::class)->only(['store','destroy','show', 'update']);

// Category
Route::post('categorys', [App\Http\Controllers\CategoryController::class, 'searchCategory']); 
Route::resource('category', App\Http\Controllers\CategoryController::class)->only(['store','destroy','show', 'update']);

// Employee
Route::post('employees', [App\Http\Controllers\EmployeeController::class, 'searchEmployee']); 
Route::resource('employee', App\Http\Controllers\EmployeeController::class)->only(['store','destroy','show', 'update']);

// customer
Route::post('customers', [App\Http\Controllers\CustomerController::class, 'searchCustomer']); 
Route::resource('customer', App\Http\Controllers\CustomerController::class)->only(['store','destroy','show', 'update']);

// Route::post('joindata', [App\Http\Controllers\ProductInfoController::class, 'joinData']); 

// expense
Route::post('expenses', [App\Http\Controllers\ExpenseController::class, 'searchExpense']); 
Route::resource('expense', App\Http\Controllers\ExpenseController::class)->only(['store','destroy','show', 'update']);

// subcategory
Route::post('subcategorys', [App\Http\Controllers\SubcategoryController::class, 'searchSubcategory']); 
Route::resource('subcategory', App\Http\Controllers\SubcategoryController::class)->only(['store','destroy','show', 'update']);

// recipe
Route::post('recipes', [App\Http\Controllers\RecipeController::class, 'searchRecipe']); 
Route::resource('recipe', App\Http\Controllers\RecipeController::class)->only(['store','destroy','show', 'update']);
