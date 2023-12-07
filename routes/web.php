<?php

use Illuminate\Support\Facades\Route;
use App\Notifications\CustomerRegistered;
use App\Notifications\NewOrder;
use App\Notifications\OrderUpdated;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});
Route::get('/test-customer-notification', function () {
    $user = App\Models\User::find(1); // Replace with a valid user ID
    $user->notify(new CustomerRegistered());
    return 'Customer notification sent.';
});

Route::get('/test-new-order-notification', function () {
    $order = App\Models\Order::find(1); // Replace with a valid order ID
    $order->user->notify(new NewOrder($order));
    return 'New order notification sent.';
});

Route::get('/test-order-updated-notification', function () {
    $order = App\Models\Order::find(1); // Replace with a valid order ID
    $order->user->notify(new OrderUpdated($order, 'Order has been updated.'));
    return 'Order updated notification sent.';
});