<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Product;
use App\Enums\CartStatusEnums;
use App\Events\CartUpdated;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
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
        $user = Auth::user();

        // Calculate the total price
        $totalPrice = $product->price * $validatedData['quantity'];

        // Use the user's active cart or create a new one
        $cart = Cart::updateOrCreate(
            ['user_id' => $validatedData['user_id'] ?? $user->id ?? null, 'status' => CartStatusEnums::PROCESSING],
            [
                'product_id' => $validatedData['product_id'],
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

        $responseData = [
            'status' => 'success',
            'message' => 'Product added to cart successfully',
            'data' => [
                'cart' => $cart,
                'product_details' => $product,
            ],
        ];

        return response()->json($responseData, 201);
    }

    public function viewSingleCart($cartId)
    {
        // Get the authenticated user or null if not authenticated
        $user = Auth::user();

        // Retrieve the single cart by its ID for the user with associated products
        $cart = Cart::where('id', $cartId)->first();
        $cart['product'] = Order::find($cart['product_id']);

        if (!$cart) {
            return response()->json([
                'status' => 'error',
                'message' => 'Cart not found or does not belong to the authenticated user',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Single cart details retrieved successfully',
            'data' => [
                'cart' => $cart,
            ],
        ]);
    }

    public function deleteSingleCart($cartId)
    {
        // Get the authenticated user or null if not authenticated
        $user = Auth::user();

        // Delete the single cart by its ID for the user
        $deletedCart = Cart::where('id', $cartId)->delete();

        if ($deletedCart) {
            return response()->json([
                'status' => 'success',
                'message' => 'Single cart deleted successfully',
            ]);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Cart not found or does not belong to the authenticated user',
        ], 404);
    }

}
