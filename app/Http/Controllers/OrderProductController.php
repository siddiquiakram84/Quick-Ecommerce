<?php

namespace App\Http\Controllers;

use App\Models\OrderProduct;
use Illuminate\Http\Request;

class OrderProductController extends Controller
{
    public function index()
    {
        $orderProducts = OrderProduct::all();
        return response()->json($orderProducts, 200);
    }

    // public function show($id)
    // {
    //     $orderProduct = OrderProduct::findOrFail($id);
    //     return response()->json($orderProduct, 200);
    // }

    // public function store(Request $request)
    // {
    //     $validatedData = $request->validate([
    //         'order_id' => 'required|exists:orders,id',
    //         'product_id' => 'required|exists:products,id',
    //         'quantity' => 'required|integer',
    //     ]);

    //     $orderProduct = OrderProduct::create($validatedData);

    //     return response()->json($orderProduct, 201);
    // }

    // public function update(Request $request, $id)
    // {
    //     $orderProduct = OrderProduct::findOrFail($id);

    //     $validatedData = $request->validate([
    //         'order_id' => 'exists:orders,id',
    //         'product_id' => 'exists:products,id',
    //         'quantity' => 'integer',
    //     ]);

    //     $orderProduct->update($validatedData);

    //     return response()->json($orderProduct, 200);
    // }

    // public function destroy($id)
    // {
    //     $orderProduct = OrderProduct::findOrFail($id);
    //     $orderProduct->delete();

    //     return response()->json(null, 204);
    // }
}
