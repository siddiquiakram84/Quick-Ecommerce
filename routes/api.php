<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\OrderProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminRegisterController;
use App\Http\Controllers\UserRegisterController;



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


// EnsureFrontendRequestsAreStateful middleware is essential for stateful authentication
Route::post('/admin-login', [AuthController::class, 'adminLogin']);
Route::post('/user-login', [AuthController::class, 'userLogin']);
Route::post('/admin-register', [AdminRegisterController::class, 'register']);
Route::post('/user-register', [UserRegisterController::class, 'register']);










// Users
Route::resource('users', UserController::class);

// Categories
Route::resource('categories', CategoryController::class);

// Orders
Route::resource('orders', OrderController::class);

// Products
Route::resource('products', ProductController::class);

// OrderProducts
Route::resource('order_products', OrderProductController::class);

// Carts
Route::resource('carts', CartController::class);

Route::middleware('auth:sanctum')->group(function () {
    Route::resource('products', 'ProductController');
    Route::resource('categories', 'CategoryController');
    Route::resource('orders', 'OrderController');
    Route::resource('customers', 'CustomerController');
});
