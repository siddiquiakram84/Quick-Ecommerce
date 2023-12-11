<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Enums\UserRoleEnums;
use App\Enums\UserStatusEnums;
// use Illuminate\Support\Str;
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

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'phone' => 'nullable|string|max:20',
        ]);

        // Set default role and status for admin users
        $request->merge(['role' => UserRoleEnums::ADMIN, 'status' => UserStatusEnums::ACTIVE]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'role' => $request->role,
            'status' => $request->status,
        ]);

        $token = $user->createToken('admin_auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Admin registered successfully',
            'token' => $token,
            'user' => $user,
        ]);
    }
}
