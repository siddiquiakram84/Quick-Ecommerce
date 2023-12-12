<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::all();
        return response()->json($orders, 200);
    }

    public function show($id)
    {
        $order = Order::findOrFail($id);
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

    public function placeOrder(Request $request): JsonResponse
    {
        // Validate inputs
        $validatedData = $request->validate([
            'user_id' => 'required|exists:users,id',
            'product_id' => 'required|array',
            'quantities' => 'required|array',
            'product_id.*' => 'exists:products,id',
            'quantities.*' => 'integer|min:1',
        ]);

        // Create a new order with the user_id
        $order = Order::create([
            'user_id' => $validatedData['user_id'],
            // Add any other order-related fields here
        ]);

        try {
            // Start a database transaction
            // Start a database transaction
        DB::beginTransaction();

        $totalPrice = 0; // Initialize total price

        // Attach products to the order with quantities
        for ($i = 0; $i < count($request->product_id); $i++) {
            $product = Product::find($request->product_id[$i]);

            // Attach the product to the order with the specified quantity
            $order->products()->attach($product, ['quantity' => $request->quantities[$i]]);

            // Calculate the total price for each product and quantity
            $totalPrice += $product->price * $request->quantities[$i];
        }

        // Update the total_price attribute of the order
        $order->update(['total_price' => $totalPrice]);

        // Commit the transaction if everything is successful
        DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'Order placed successfully',
            'data' => $validatedData,
            'total_price' => $totalPrice,
            'order_id' => $order->id,
        ], 200);
    }
         catch (\Exception $e) {
            // Rollback the transaction in case of any error
            DB::rollback();

            return response()->json([
                'message' => 'Failed to place order',
                'error' => [
                    'code' => $e->getCode(),
                    'message' => $e->getMessage(),
                    'trace' => $e->getTrace(),
                ]
            ], 500);
        }
    }

    public function viewOrders()
    {
        // Assume you have an authentication system, and you get the user ID from the authenticated user
        $userId = auth()->id();

        // Fetch orders for the authenticated user
        $orders = Order::where('user_id', $userId)->get();

        return response()->json(['orders' => $orders], 200);
    }

    public function viewOrder($id)
    {
        $order = Order::find($id);

        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        return response()->json(['order' => $order], 200);
    }

}
