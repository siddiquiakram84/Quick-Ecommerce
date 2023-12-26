<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Cart;
use App\Models\Product;
use App\Enums\CartStatusEnums;
use Illuminate\Support\Facades\Auth;;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{

    
    public function index(){
        // Get the authentic user only
        $user = Auth::user();
        // Get all orders from the database
        $orders = Order::all();
        $resp['success'] = true;
        $resp['message'] = 'Orderes retrieved successfully.';
        $resp['data'] = $orders;
        
        // Return a response, e.g., as JSON
        return response()->json([$resp], 200);
    }

    public function indexView(Request $request)
    {   // Get the authenticated user
        $user = Auth::user();
        
        $user_id = $user->id;
        
        $cart = Cart::where('user_id', $user_id)->first();
        // Get the current cart items
        $cartItems = $cart->cartitem ?? [];
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
        $orders = Order::where('id', $request->id)->get();
        $resp['status'] = true;
        $resp['message'] = 'Order details retrieved successfully.';
        $resp['data'] = [$orders, $formattedCartItems];
        return response()->json($resp, 200);
    }
    
    public function show()
    {
        
        // Get the authenticated user
        $user = Auth::user();
        // Get the cart items for the user with product details
        $order = Order::where('user_id', $user->id)->get();
        
        $cart = Cart::where('user_id', $user->id)->first();
        // Get the current cart items
        $cartItems = $cart->cartitem ?? [];
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
        $resp['message'] = 'Order details retrieved successfully.';
        $resp['data'] = [$order, $formattedCartItems];
        return response()->json($resp, 200);
    }
    
    public function store(Request $request)
    {
        // Validate the incoming request data
        $validatedData = $request->validate([
            'user_id' => 'required|exists:users,id',
            'total_price' => 'required|numeric',
            'status' => 'required|string|in:Delivered,Pending,Processing',
            'payment_status' => 'required|integer|in:0, 1', // 0 for pending, 1 for paid.
            'delivery_address' => 'nullable|string',
            'delivery_method' => 'nullable|string',
        ]);

        // Create a new order in the database
        $order = Order::create($validatedData);

        // Return a response, e.g., as JSON
        return response()->json($order, 201); // 201 Created
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

    public function placeOrder(Request $request)
    {
        $validatedData = $request->validate([
            'user_id' => 'required|exists:users,id',
            'cart_id' => 'sometimes|exists:carts,id',
        ]);

        // Get the authenticated user
        $user = Auth::user();

        // Get the cart items for the user with product details
        // $cartItems = Cart::where('user_id', $user->id)->with('products')->get();
        $cart = Cart::where('id', $validatedData['cart_id'])->where('user_id', $validatedData['user_id'])->first();
        
        // Get the current cart items
        $cartItems = $cart->cartitem ?? [];
        // dd($cartItems);
        // Calculate the total price based on the prices of items in the cart
        $totalPrice = $cart->total_price;
    
        // Use constants for order status and payment status
        $paymentStatusUnpaid = 0;
        // Check if the cart exists
        if ($cart) {
            // Check if the current status is 2
            if ($cart->status == CartStatusEnums::PENDING) {
                // Create a new order
                $order = Order::create([
                    'user_id' => $validatedData['user_id'],
                    'total_price' => $totalPrice,
                    'status' => CartStatusEnums::COMPLETED,
                    'payment_status' => $paymentStatusUnpaid,
                    'delivery_address' => $request->input('delivery_address'),
                    'delivery_method' => $request->input('delivery_method'),
                ]);
            }
        }

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


            // dd($formattedCartItems);

            // Prepare the response data 
            $responseData = [
                'order_id' => $order->id,
                'total_price' => $order->total_price,
                'status' => $order->status,
                'payment_status' => $order->payment_status,
                'delivery_address' => $order->delivery_address,
                'delivery_method' => $order->delivery_method,
                'created_at' => $order->created_at->toDateTimeString(),
                'products' => $formattedCartItems,
                
            ];
            // Update the cart status as completed
            // Check if the cart exists
            if ($cart) {
                // Update the status attribute to 1
                $cart->update(['status' => CartStatusEnums::COMPLETED]);
        
            // Return the JSON response
            $resp['status'] = true;
            $resp['message'] = 'Order placed successfully';
            $resp['data'] = $responseData;

            return response()->json([$resp], 200);
    }
    

    }
}