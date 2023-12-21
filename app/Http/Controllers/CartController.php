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
            'cart_id' => 'sometimes|exists:carts,id',
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);
    
        // Find the product
        $product = Product::findOrFail($validatedData['product_id']);
    
        $user_id = $validatedData['user_id'] ?? null;
        $cart_id = $validatedData['cart_id'] ?? null;
        $cart = null;
    
        // If cart_id is provided, try to find an existing cart
        if ($cart_id) {
            $cart = Cart::find($cart_id);
            $cart->status = CartStatusEnums::PENDING; // Replace 'new_status' with the desired new status value
            $cart->save();
        }
    
        // If no cart found and user_id is provided, try to find an existing cart by user_id
        if (!$cart && $user_id) {
            $cart = Cart::where('user_id', $user_id)->first();
        }
    
        // If no cart found, or if both cart_id and user_id are null, create a new cart
        if (!$cart) {
            $cart = new Cart([
                'user_id' => $user_id,
                'cartitem' => [],
            ]);
    
            $cart->save();
        } elseif ($user_id && $cart->user_id !== $user_id) {
            $resp['status'] = false;
            $resp['message'] = 'Invalid Request, Please Try Again.';
            return response()->json([$resp], 400);
            // If user_id is provided and differs from the current user_id in the cart, update it
            // $cart->update(['user_id' => $user_id]);
        }
    
        // Get the current cart items
        $cartItems = $cart->cartitem ?? [];
    
        // Check if the product already exists in the cart
        $existingItemIndex = array_search($validatedData['product_id'], array_column($cartItems, 'product_id'));
    
        // Update quantity if the product is already in the cart, otherwise add a new item
        if ($existingItemIndex !== false) {
            $cartItems[$existingItemIndex]['quantity'] += $validatedData['quantity'];
        } else {
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
        
        $cart = Cart::find($cartId);

        if (!$cart) {
            // Handle the case where the cart is not found
            $resp['status'] = false;
            $resp['message'] = 'Cart not found';
            $resp['data'] = $cart;
            return response()->json([$resp], 404);
        }
 
        $cartItemsWithStatusCompleted = Cart::find($cartId);

        if ($cartItemsWithStatusCompleted && $cartItemsWithStatusCompleted->status == 2) {
            $resp['status'] = true;
            $resp['message'] = 'No product found';
            $resp['data'] = [];
            return response()->json([$resp], 200);
        }
        else
        {

        // Access the cartitem attribute from the model
        $cartItems = $cart->cartitem;

        // Fetch product details for each cart item
        $formattedCartItems = [];

        foreach ($cartItems as $cartItem) {
            // Find the Product model by its ID
            $product = Product::find($cartItem['product_id']);

            if ($product) {
                // Add formatted cart item to the result array
                $formattedCartItems[] = [
                    'product_id' => $cartItem['product_id'],
                    'quantity' => $cartItem['quantity'],
                    'product' => $product,
                ];
            }
        }

        $resp['status'] = true;
        $resp['message'] = 'Cart retrieved successfully';
        $resp['data'] = $formattedCartItems;

        return response()->json([$resp], 200);
        }
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