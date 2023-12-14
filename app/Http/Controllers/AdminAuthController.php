<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Enums\UserRoleEnums;
use App\Enums\UserStatusEnums;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AdminAuthController extends Controller
{
    
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            if ($user->role == UserRoleEnums::ADMIN) {
                $token = $user->createToken('admin_auth_token')->plainTextToken;

                return response()->json([
                    'success' => true,
                    'message' => 'Login successful',
                    'token' => $token,
                    'user' => $user,
                ]);
            }
        }

        return response()->json([
            'success' => false,
            'message' => 'Invalid credentials',
        ], 401);
    }

    public function logout(Request $request)
    {
        $user = $request->user();

        // Revoke the user's access token
        $user->tokens()->where('id', $user->currentAccessToken()->id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Admin logout successful.',
        ]);
    }

    public function adminMe(Request $request)
    {
        $admin = $request->user();

        if ($admin->role == UserRoleEnums::ADMIN) {
            return response()->json([
                'success' => true,
                'message' => 'Admin details retrieved successfully.',
                'user' => $admin,
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Invalid credentials',
        ], 401);
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
        ]);

        // Create a new admin user
        $admin = User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
            'role' => UserRoleEnums::ADMIN,
            // Add other fields as needed
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Admin registered successfully.',
            'user' => $admin,
        ], 201);
    }

}
