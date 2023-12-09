<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AdminAuthController extends Controller
{
    public function register(Request $request)
    {
        // Validate incoming request data
        $validatedData = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'phone' => 'nullable|string|max:20',
            'role' => 'required|in:0,1', // 0 for user, 1 for admin
            'status' => 'required|in:0,1', // 0 for inactive, 1 for active
        ]);

        // Create a new user with the validated data
        $newUser = User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
            'phone' => $validatedData['phone'],
            'role' => $validatedData['role'],
            'status' => $validatedData['status'],
        ]);

        // Return a JSON response with the registered user and a success message
        return response()->json(['user' => $newUser, 'message' => 'User registered successfully.'], 201);
    }
    
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            // Check if the user has the 'admin' role
            if ($user->role == User::ROLE_ADMIN) {
                $token = $user->createToken('admin_auth_token')->plainTextToken;

                return response()->json([
                    'success' => true,
                    'message' => 'Login successful',
                    'token' => $token,
                    'user' => $user,
                ]);
            }
        }

        // If authentication fails
        return response()->json([
            'success' => false,
            'message' => 'Invalid credentials',
        ], 401);
    }
}
