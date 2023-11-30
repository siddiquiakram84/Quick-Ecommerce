<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        $carts = Cart::all();
        return response()->json($carts, 200);
    }

    public function show($id)
    {
        $cart = Cart::findOrFail($id);
        return response()->json($cart, 200);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'user_id' => 'required|exists:users,id',
            'status' => 'required|integer',
            'total_price' => 'required|numeric',
        ]);

        $cart = Cart::create($validatedData);

        return response()->json($cart, 201);
    }

    public function update(Request $request, $id)
    {
        $cart = Cart::findOrFail($id);

        $validatedData = $request->validate([
            'user_id' => 'exists:users,id',
            'status' => 'integer',
            'total_price' => 'numeric',
        ]);

        $cart->update($validatedData);

        return response()->json($cart, 200);
    }

    public function destroy($id)
    {
        $cart = Cart::findOrFail($id);
        $cart->delete();

        return response()->json(null, 204);
    }
}
