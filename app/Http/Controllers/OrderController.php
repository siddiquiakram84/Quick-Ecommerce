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
        $orders = Order::with('products')->get();
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



}
