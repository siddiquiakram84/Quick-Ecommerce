<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use App\Models\Product;
use App\Events\CartUpdated;
use Illuminate\Http\Request;
use App\Enums\CartStatusEnums;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CartController extends Controller
{
    public function addToCart(Request $request)
    {
        $validatedData = $request->validate([
            'user_id' => 'sometimes|exists:users,id',
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        // Find the product
        $product = Product::findOrFail($validatedData['product_id']);

        $user_id = $validatedData['user_id'] ?? null;
        $cart = Cart::where('user_id', $user_id)->first();

        if (!$cart) {
            $cart = new Cart([$validatedData['user_id'] ?? null, 'cartitem' => []]);
            $cart->save();
        }

        // Get the current cart items
        $cartItems = $cart->cartitem ?? [];

        // Check if the product already exists in the cart
        $existingItemIndex = array_search($validatedData['product_id'], array_column($cartItems, 'product_id'));

        if ($existingItemIndex !== false) {
            // Update quantity if the product is already in the cart
            $cartItems[$existingItemIndex]['quantity'] += $validatedData['quantity'];
        } else {
            // Add a new item to the cart
            $cartItems[] = ['product_id' => $validatedData['product_id'], 'quantity' => $validatedData['quantity']];
        }
        
        // Update the cart with the new cart items
        $cart->update(['cartitem' => $cartItems]);

        // Recalculate total price in the cart if needed
        $totalPrice = 0;
        foreach ($cartItems as $item) {
            $product_id = $item["product_id"];
            $quantity = $item["quantity"];

            // Fetch the product by product_id
            $product = Product::find($product_id);

            if ($product) {
                // Calculate the total price for this item
                $itemTotalPrice = $quantity * $product->price;

                // Add the item's total price to the overall total price
                $totalPrice += $itemTotalPrice;
            }
        }
        // Update the total_price column in the cart model
        $cart->update(['total_price' => $totalPrice]);
        $resp['success'] = true;
        $resp['data'] = $cart;

        return response()->json([$resp], 200);
    }


    public function deleteSingleCart($cartId)
    {

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

    public function viewSingleCart($cartId)
    {
        $cartItem = Cart::find($cartId);

        // Access the cartitem attribute from the model
        $cartItemData = $cartItem->cartitem;

        // Fetch product details for each cart item
        $formattedCartItems = array_map(function ($cartItem) {
            $product = Product::find($cartItem['product_id']);

            return [
                'product_id' => $cartItem['product_id'],
                'quantity' => $cartItem['quantity'],
                'product' => $product,
            ];
        }, $cartItemData);

        return response()->json(['message' => 'Cart retrieved successfully', 'cart' => $formattedCartItems]);
    }


    public function removeProductFromCart(Request $request)
    {
        $validateData = $request->validate([
            'user_id' => 'sometimes|exists:users,id',
            'product_id' => 'required|exists:products,id',
            'cart_id' => 'required|exists:carts,id'
        ]);

        $productId = $validateData['product_id'];


        $cart = Cart::find($validateData['cart_id']);

        $cartItems = $cart->cartitem;

        $existingItemIndex = array_search($productId, array_column($cartItems, 'product_id'));

        if ($existingItemIndex !== false) {
            // Remove the product from the cart
            array_splice($cartItems, $existingItemIndex, 1);

            // Update the cart with the updated cart items
            $cart->update(['cartitem' => $cartItems]);
            $resp['success'] = true;
            $resp['message'] = 'Product removed from cart successfully';
            $resp['data'] = $cartItems;
            return response()->json([$resp], 200);
        } 
        else 
        {
        return response()->json(['message' => 'Product not found in the cart'], 404);
        }
        return response()->json(['message' => 'Cart not found'], 404);

    }

}