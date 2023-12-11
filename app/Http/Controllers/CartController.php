<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    const CART_STATUS_ACTIVE = 1;

    public function addToCart(Request $request)
    {
        // Validate inputs
        $validatedData = $request->validate([
            'user_id' => 'sometimes|exists:users,id', // Optional user_id
            'product_id' => 'required|exists:products,id',
            'order_id' => 'sometimes|exists:orders,id', // Optional order_id
            'quantity' => 'required|integer|min:1',
        ]);

        // Find the product
        $product = Product::findOrFail($validatedData['product_id']);

        // Check if the product exists
        if (!$product) {
            return response()->json([
                'status' => 'error',
                'message' => 'Product not found',
            ], 404);
        }

        // Get the authenticated user or null if not authenticated
        $user = auth()->user();
        
        // Calculate the total price
        $totalPrice = $product->price * $validatedData['quantity'];

        // Create or update the cart entry for the user
        $cart = Cart::updateOrCreate(
            ['user_id' =>  $validatedData['user_id'] ?? null, 'status' => self::CART_STATUS_ACTIVE],
            [
                'order_id' => $validatedData['order_id'] ?? null,
                'total_price' => DB::raw("total_price + {$totalPrice}"),
                'product_id' => $validatedData['product_id']
            ]
        );

        // Attach the product to the cart
        $cart->products()->syncWithoutDetaching([
            $validatedData['product_id'] => [
                'quantity' => $validatedData['quantity'],
                'unit_price' => $product->price,
            ],
            
        ]);

        // Reload the cart to get the updated total_price value
        $cart->refresh();

        // Return a success response
        return response()->json([
            'status' => 'success',
            'message' => 'Product added to cart successfully',
            'data' => [
                'cart' => $cart,
            ],
        ], 201);
    }


}
