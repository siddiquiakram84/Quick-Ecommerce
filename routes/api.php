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
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

// Route::post('/user', function(){
//     return response()->json("Post api hit sucessfully");
// });

// Route::delete('/user/{id}', function($id){
//     return response("delete" . $id, 200);
// });
// Route::put('/user/{id}', function($id){
//     return response('Put'. $id, 200);
// });
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
