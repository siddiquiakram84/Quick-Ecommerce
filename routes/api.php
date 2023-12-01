<?php

use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\UserAuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\OrderProductController;
use App\Http\Controllers\CartController;

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

// Group for Admin routes
Route::prefix('admin')->group(function () {
    Route::post('/login', [AdminAuthController::class, 'login']);
    Route::post('/register', [AdminAuthController::class, 'register']);
    Route::get('admin/dashboard', [AdminDashboardController::class, 'index']); // Add AdminDashboardController
});

// User Routes
Route::prefix('users')->group(function () {
    Route::post('/login', [UserAuthController::class, 'login']);
    Route::post('/register', [UserAuthController::class, 'register']);
});

// Resource Routes
Route::middleware('auth:sanctum')->group(function () {
    // Users
    Route::resource('users', UserController::class)->only(['index', 'show', 'store', 'update', 'destroy']);

    // Categories
    Route::resource('categories', CategoryController::class)->except(['create', 'edit']);

    // Orders
    Route::resource('orders', OrderController::class)->except(['create', 'edit']);

    // Products
    Route::resource('products', ProductController::class)->except(['create', 'edit']);

    // OrderProducts
    Route::resource('order/products', OrderProductController::class)->except(['create', 'edit']);

    // Carts
    Route::resource('carts', CartController::class)->except(['create', 'edit']);
});
