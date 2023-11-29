<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserRegisterController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|min:6',
            'phone' => 'required|string|max:20',
            // Add other validation rules as needed
        ]);

        $user = User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
            'phone' => $request->input('phone'),
            'role' => 0, // Assuming 0 represents the role for non-admin users
            'status' => 1, // Assuming 1 represents an active status
            // Add other fields as needed
        ]);

        return response()->json(['message' => 'User registered successfully', 'user' => $user], 201);
    }
}