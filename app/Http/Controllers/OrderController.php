<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

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
            'payment_status' => 'required|string|in:Paid,Pending,Processing',
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
            'user_id' => 'exists:users,id',
            'total_price' => 'numeric',
            'status' => 'string|in:Delivered,Pending,Processing',
            'payment_status' => 'string|in:Paid,Pending,Processing',
            'delivery_address' => 'nullable|string',
            'delivery_method' => 'nullable|string',
        ]);

        $order->update($validatedData);

        return response()->json($order, 200);
    }

    public function destroy($id)
    {
        $order = Order::findOrFail($id);
        $order->delete();

        return response()->json(null, 204);
    }

    public function placeOrder(Request $request)
    {
        // Assume the request contains product_ids and quantities for the ordered products
        $productIds = $request->input('product_ids');
        $quantities = $request->input('quantities');

        // Validate inputs
        $request->validate([
            'product_ids' => 'required|array',
            'quantities' => 'required|array',
            'product_ids.*' => 'exists:products,id',
            'quantities.*' => 'integer|min:1',
        ]);

        // Logic to place an order (you may save this information in the database)

        return response()->json(['message' => 'Order placed successfully']);
    }

    public function viewOrders()
    {
        // Assume you have an authentication system, and you get the user ID from the authenticated user
        $userId = auth()->id();

        // Fetch orders for the authenticated user
        $orders = Order::where('user_id', $userId)->get();

        return response()->json(['orders' => $orders]);
    }

    public function viewOrder($id)
    {
        $order = Order::find($id);

        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        return response()->json(['order' => $order]);
    }
}
