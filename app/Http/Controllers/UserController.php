<?php

namespace App\Http\Controllers;

use App\Enums\UserRoleEnums;
use App\Enums\UserStatusEnums;
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

    public function adminShow()
    {   $role = UserRoleEnums::USER;
        $users = User::where('role', $role)->get();
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
            'role' => ['required', Rule::in([UserRoleEnums::USER, UserRoleEnums::ADMIN])],
            'status' => ['required', Rule::in([UserStatusEnums::ACTIVE, UserStatusEnums::INACTIVE])],
        ]);

        $user = User::create($validatedData);

        return response()->json($user, 201);
    }

    public function update(Request $request, $id)
    {
        // Validate inputs
        $validatedData = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email,' . $id,
            // Add other fields as needed
        ]);
    
        // Find the user
        $user = User::findOrFail($id);
    
        // Update the user with validated data
        $user->update($validatedData);
    
        // Return a success response
        return response()->json(['message' => 'User updated successfully', 'user' => $user], 200);
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

    public function listProducts()
    {
        $products = Product::all();

        return response()->json(['products' => $products]);
    }

    }

