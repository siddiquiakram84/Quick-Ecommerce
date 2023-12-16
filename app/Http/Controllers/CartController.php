<?php

namespace App\Http\Controllers;

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    const CART_STATUS_ACTIVE = 1;

    public function addToCart(Request $request)
    {
        $validatedData = $request->validate([
            'user_id' => 'sometimes|exists:users,id',
            'product_id' => 'required|exists:products,id',
            'order_id' => 'sometimes|exists:orders,id',
            'quantity' => 'required|integer|min:1',
        ]);

        // Find the product
        $product = Product::findOrFail($validatedData['product_id']);

        // Get the authenticated user or null if not authenticated
        $user = auth()->user();

        // Calculate the total price
        $totalPrice = $product->price * $validatedData['quantity'];

        // Use the user's active cart or create a new one
        $cart = Cart::updateOrCreate(
            ['user_id' => $validatedData['user_id'] ?? $user->id ?? null, 'status' => self::CART_STATUS_ACTIVE],
            [
                'order_id' => $validatedData['order_id'] ?? null,
            ]
        );

        // Attach the product to the cart with quantity
        $cart->products()->syncWithoutDetaching([
            $validatedData['product_id'] => [
                'quantity' => $validatedData['quantity'],
                'unit_price' => $product->price,
            ],
        ]);

        // Reload the cart to get the updated total_price value
        $cart->refresh();

        return response()->json([
            'status' => 'success',
            'message' => 'Product added to cart successfully',
            'data' => [
                'cart' => $cart,
            ],
        ], 201);
    }
}
