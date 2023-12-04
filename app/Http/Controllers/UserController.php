<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Product;
use App\Models\Cart;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderProduct;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function index()
    {
        $users = User::all();
        return response()->json($users, 200);
    }

    public function show($id)
    {
        $user = User::findOrFail($id);
        return response()->json($user, 200);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'phone' => 'nullable|string|max:20',
            'role' => 'required|string',
            'status' => 'required|integer',
        ]);

        $user = User::create($validatedData);

        return response()->json($user, 201);
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validatedData = $request->validate([
            'name' => 'string|max:255',
            'email' => ['email', Rule::unique('users')->ignore($user->id)],
            'password' => 'string|min:6',
            'phone' => 'nullable|string|max:20',
            'role' => 'string',
            'status' => 'integer',
        ]);

        $user->update($validatedData);

        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'user' => [
                'id' => $user->id,
                'username' => $user->name,
                'email' => $user->email,
            ],
        ]);
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }
        $user->delete();

        return response()->json(['user' => $user, 'message' => 'User deleted successfully'], 200);
    }

    public function listCategories()
    {
        $categories = Category::all();

        return response()->json(['categories' => $categories]);
    }

    public function listProducts()
    {
        $products = Product::all();

        return response()->json(['products' => $products]);
    }

    public function viewProduct($id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        return response()->json(['product' => $product]);
    }

    public function addToCart(Request $request)
    {
        // Assume the request contains product_id and quantity
        $productId = $request->input('product_id');
        $quantity = $request->input('quantity');

        // Validate inputs
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        // Logic to add the product to the user's cart (you may save this information in the database)

        return response()->json(['message' => 'Product added to cart successfully']);
    }

    public function placeOrder(Request $request)
    {
        // Assume the request contains product_ids and quantities for the ordered products
        $productIds = $request->input('product_ids');
        $quantities = $request->input('quantities');

        // Validate inputs
        $request->validate([
            'product_ids' => 'required|array',
            'quantities' => 'required|array',
            'product_ids.*' => 'exists:products,id',
            'quantities.*' => 'integer|min:1',
        ]);

        // Logic to place an order (you may save this information in the database)

        return response()->json(['message' => 'Order placed successfully']);
    }

    public function viewOrders()
    {
        // Assume you have an authentication system, and you get the user ID from the authenticated user
        $userId = auth()->id();

        // Fetch orders for the authenticated user
        $orders = Order::where('user_id', $userId)->get();

        return response()->json(['orders' => $orders]);
    }

    }

