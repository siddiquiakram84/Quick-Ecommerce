<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{

    public function addToCart(Request $request)
    {
        try {
            // Validate inputs
            $validatedData = $request->validate([
                'product_id' => 'required|exists:products,id',
                'quantity' => 'required|integer|min:1',
            ]);

            // Get the authenticated user
            $user = auth()->user();

            // Find the product
            $product = Product::findOrFail($validatedData['product_id']);

            // Calculate the total price
            $totalPrice = $product->price * $validatedData['quantity'];

            // Create or update the cart entry for the user
            $cart = Cart::updateOrCreate(
                ['user_id' => $user->id, 'status' => 'active'],
                ['total_price' => DB::raw("total_price + {$totalPrice}")]
            );

            // Attach the product to the cart
            $cart->products()->attach($validatedData['product_id'], [
                'quantity' => $validatedData['quantity'],
                'unit_price' => $product->price,
            ]);

            // Return a success response
            return response()->json([
                'status' => 'success',
                'message' => 'Product added to cart successfully',
                'data' => [
                    'cart' => $cart,
                ],
            ], 201);
        } catch (ValidationException $e) {
            // Return a validation error response
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            // Log the exception for debugging purposes
            logger()->error('Failed to add product to cart', ['exception' => $e]);

            // Return a generic error response
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to add product to cart',
            ], 500);
        }
    }

}
