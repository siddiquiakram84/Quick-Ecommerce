<?php

namespace App\Http\Controllers;

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Product;
use App\Enums\CartStatusEnums;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    // const CART_STATUS_ACTIVE = 1;

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
            ['user_id' => $validatedData['user_id'] ?? $user->id ?? null, 'status' => CartStatusEnums::PROCESSING],
            [ 'product_id' => $validatedData['product_id'],
                'order_id' => $validatedData['order_id'] ?? null,
                'total_price' => $totalPrice,
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
        
        $resp['data'] = $cart;
        $resp['product_details'] = $product;

        return response()->json([
            'status' => 'success',
            'message' => 'Product added to cart successfully',
            $resp], 201);
    }

    public function viewCart()
    {
        // Get the authenticated user or null if not authenticated
        $user = Auth::user();

        // Retrieve the active cart for the user
        $cart = Cart::where('user_id', $user->id ?? null)
            ->orderByDesc('created_at') // Assuming you want the latest cart
            ->firstorFail();

        if (!$cart) {
            return response()->json([
                'status' => 'success',
                'message' => 'Cart is empty',
                'data' => [
                    'cart' => null,
                ],
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Cart details retrieved successfully',
            'data' => [
                'cart' => $cart,
            ],
        ]);
    }
}
