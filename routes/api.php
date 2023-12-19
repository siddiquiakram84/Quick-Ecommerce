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
use App\Models\Category;

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


// Home page features
Route::get('/products/search', [ProductController::class, 'search']);
Route::post('/cart/add', [CartController::class, 'addToCart']);
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/products', [ProductController::class, 'index']);
Route::get('/product/{id}', [ProductController::class, 'show']);
Route::get('/category/{id}', [CategoryController::class, 'show']);
Route::get('/cart/{cartId}', [CartController::class, 'viewSingleCart']);
Route::delete('/cart/{cartId}', [CartController::class, 'deleteSingleCart']);

// Group for Admin routes
Route::prefix('admin')->group(function () {
    Route::post('/login', [AdminAuthController::class, 'login']);
    Route::post('/register', [AdminAuthController::class, 'register']);
    Route::get('/logout', [AdminAuthController::class, 'logout']);

    // Admin routes for managing customers/users
    Route::get('/user', [UserController::class, 'userShow']);
    Route::post('/user', [UserController::class, 'store']);
    Route::put('/user/{id}', [UserController::class, 'update']);
    Route::delete('/user/{id}', [UserController::class, 'destroy']);
    Route::get('/category/{id}', [CategoryController::class, 'show']);

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
Route::post('/user/login', [UserAuthController::class, 'login']);
Route::post('/user/register', [UserAuthController::class, 'register']);


Route::group(['middleware' => ['auth:sanctum']], function () {

    Route::post('/user/place-order', [OrderController::class, 'placeOrder']);
    Route::get('/user/orders', [OrderController::class, 'show']);
    Route::post('/user/logout', [UserAuthController::class, 'logout']);
    Route::post('/admin/logout', [AdminAuthController::class, 'logout']);
    Route::get('admin/me', [AdminAuthController::class, 'adminMe']);
});

Route::prefix('user')->group(function () { 
    
    Route::get('/orders/{id}', [OrderController::class, 'index']);
    Route::post('/cart/add', [CartController::class, 'addToCart']);
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::get('/products', [ProductController::class, 'index']);
    Route::get('/product/{id}', [ProductController::class, 'show']);
    Route::get('/category/{id}', [CategoryController::class, 'show']);
    Route::get('/cart/{id}', [CartController::class, 'viewSingleCart']);
    Route::delete('/cart/{id}', [CartController::class, 'deleteSingleCart']);
});
