<?php

use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\UserAuthController;
use Illuminate\Support\Facades\Route;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\OrderProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\DashboardController;

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


// Group for Admin routes

Route::prefix('admin')->group(function () {
    Route::post('/login', [AdminAuthController::class, 'login']);
    Route::post('/register', [AdminAuthController::class, 'register']);

    // Admin routes for managing customers/users
    Route::get('/user', [UserController::class, 'userShow']);
    Route::post('/user', [UserController::class, 'store']);
    Route::put('/user/{id}', [UserController::class, 'update']);
    Route::delete('/user/{id}', [UserController::class, 'destroy']);

    // Admin routes for managing products
    Route::resource('products', ProductController::class);

    // Admin routes for managing categories
    Route::resource('categories', CategoryController::class);

    // Admin routes for managing orders      
    Route::resource('orders', OrderController::class);

    // Admin Dashboard route
    Route::get('/dashboard', [DashboardController::class, 'index']);

    // Order history
    Route::get('/history', [OrderProductController::class, 'index']);

});

Route::prefix('user')->group(function () {
    Route::post('/login', [UserAuthController::class, 'login']);
    Route::post('/register', [UserAuthController::class, 'register']);
    Route::post('/order/place', [OrderController::class, 'placeOrder']);
    Route::get('/orders', [OrderController::class, 'viewOrders']);
    Route::get('/orders/{id}', [OrderController::class, 'viewOrder']);
    Route::post('/cart/add', [CartController::class, 'addToCart']);
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::get('/products', [ProductController::class, 'index']);
    Route::get('/product/{id}', [ProductController::class, 'show']);

});

// Home page features
Route::post('/cart/add', [CartController::class, 'addToCart']);
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/products', [ProductController::class, 'index']);
Route::get('/product/{id}', [ProductController::class, 'viewProduct']);

// In your routes file
Route::middleware('auth:sanctum')->get('/view-orders', [OrderController::class, 'viewOrders']);

Route::middleware('auth:sanctum')->group(function () {
    Route::resource('orders', 'OrderController');
    Route::resource('user', 'UserController');
});
