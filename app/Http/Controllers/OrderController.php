<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Cart;
use Illuminate\Support\Facades\Auth;;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with('products')->get();
        return response()->json($orders, 200);
    }

    public function show(Request $request)
    {
        // Get the authenticated user
        $user = Auth::user();

        // Get the cart items for the user with product details
        $order = Order::findOrFail('user_id', $user->id)->with('products')->get();
        return response()->json($order, 200);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'user_id' => 'required|exists:users,id',
            'total_price' => 'required|numeric',
            'status' => 'required|string|in:Delivered,Pending,Processing',
            'payment_status' => 'required|integer|in:0, 1', // 0 for pending, 1 for paid.
            'delivery_address' => 'nullable|string',
            'delivery_method' => 'nullable|string',
        ]);

        $order = Order::create($validatedData);

        return response()->json($order, 201);
    }

    public function update(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        $validatedData = $request->validate([
            'user_id' => 'required|exists:users,id',
            'total_price' => 'required|numeric',
            'status' => 'required|string|in:Delivered,Pending,Processing',
            'payment_status' => 'required|integer|in:0, 1', // 0 for pending, 1 for paid.
            'delivery_address' => 'nullable|string',
            'delivery_method' => 'nullable|string',
        ]);

        $order->update($validatedData);

        return response()->json(['message' => 'Order updated successfully', 'order' => $order], 200);
    }

    public function destroy($id)
    {
        $order = Order::findOrFail($id);
        $order->delete();

        return response()->json(null, 204);
    }

    public function placeOrder(Request $request)
    {
        // Get the authenticated user
        $user = Auth::user();
    
        // Get the cart items for the user with product details
        $cartItems = Cart::where('user_id', $user->id)->with('products')->get();
    
        // Calculate the total price based on the prices of items in the cart
        $totalPrice = $cartItems->sum(function ($cartItem) {
            return $cartItem->total_price;
        });
    
        // Use constants for order status and payment status
        $orderStatusPending = 'Pending';
        $paymentStatusUnpaid = 0;
    
        // Create a new order
        $order = Order::create([
            'user_id' => $user->id,
            'total_price' => $totalPrice,
            'status' => $orderStatusPending,
            'payment_status' => $paymentStatusUnpaid,
            'delivery_address' => $request->input('delivery_address'),
            'delivery_method' => $request->input('delivery_method'),
        ]);
    
        // Initialize an array to store detailed product information
        $productsDetails = [];
    
        // Attach products to the order with pivot data
        // Attach products to the order with pivot data
        foreach ($cartItems as $cartItem) {
            // Check if the product relationship is not null
            if ($cartItem->product) {
                // Attach the product to the order
                $order->products()->attach($cartItem->product_id, [
                    'quantity' => $cartItem->pivot->quantity,
                    'unit_price' => $cartItem->pivot->unit_price,
                ]);

                // Store detailed product information
                $productDetails = [
                    'product_id' => $cartItem->product->id,
                    'name' => $cartItem->product->name,
                    'description' => $cartItem->product->description,
                    'price' => $cartItem->product->price,
                    'quantity' => $cartItem->pivot->quantity,
                    'unit_price' => $cartItem->pivot->unit_price,
                ];

                $productsDetails[] = $productDetails;
            } 

                // Removing the item from the cart after placing the order
                $cartItem->delete();
            }

            // Prepare the response data 
            $responseData = [
                'order_id' => $order->id,
                'total_price' => $order->total_price,
                'status' => $order->status,
                'payment_status' => $order->payment_status,
                'delivery_address' => $order->delivery_address,
                'delivery_method' => $order->delivery_method,
                'created_at' => $order->created_at->toDateTimeString(),
                'products' => $productsDetails,
                'message' => 'Order placed successfully',
            ];
        
            // Return the JSON response
            return response()->json($responseData, 200);
    }
    

}
