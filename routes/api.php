<?php

use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\UserAuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\OrderProductController;
use App\Http\Controllers\CartController;
use App\Models\Product;

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

    // Admin routes for managing products
    Route::resource('products', ProductController::class);

    // Admin routes for managing categories
    Route::resource('categories', CategoryController::class);

    // Admin routes for managing orders
    Route::resource('orders', OrderController::class);

    // Admin routes for managing customers
    Route::resource('customers', UserController::class);

    // Admin Dashboard route
    Route::get('/dashboard', [DashboardController::class, 'index']);

});

// Group for User routes
Route::prefix('user')->group(function () {
    Route::post('/login', [UserAuthController::class, 'login']);
    Route::post('/register', [UserAuthController::class, 'register']);

    // User routes for viewing categories and products
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::get('/products', [ProductController::class, 'index']);
    Route::get('/products/{id}', [ProductController::class, 'show']);

    // User routes for managing the shopping cart
    Route::post('/cart/add', [CartController::class, 'addProduct']);
    Route::get('/my-orders', [OrderController::class, 'userOrders']);
    Route::post('/orders/place', [OrderController::class, 'placeOrder']);
});









// Users
Route::resource('users', UserController::class);

// Categories
Route::resource('categories', CategoryController::class);

// Orders
Route::resource('orders', OrderController::class);

// Products
Route::resource('products', ProductController::class);

// OrderProducts
Route::resource('order/products', OrderProductController::class);

// Carts
Route::resource('carts', CartController::class);

Route::middleware('auth:sanctum')->group(function () {
    Route::resource('products', 'ProductController');
    Route::resource('categories', 'CategoryController');
    Route::resource('orders', 'OrderController');
    Route::resource('customers', 'CustomerController');
});
