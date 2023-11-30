<?php

namespace App\Http\Controllers;

use App\Models\User;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminAuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users', // Assuming 'users' table is used for both admin and non-admin users
            'password' => 'required|min:6',
            'phone' => 'nullable|string|max:20',
            'role' => 'required|string', // Assuming role is required for admin
            'status' => 'required|integer',
            // Add any other validation rules as needed
        ]);

        $admin = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'role' => $request->role,
            'status' => $request->status,
            'remember_token' => Str::random(10), // Include this line if necessary
        ]);

        return response()->json(['admin' => $admin], 201);
    }
    
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::guard('web')->attempt($credentials)) {
            $user = Auth::user();

            // Check if the user has the 'admin' role    "'"
            if ($user->role == 'admin') {
                $token = $user->createToken('admin_auth_token')->plainTextToken;

                return response()->json(['token' => $token], 200);
            }
        }

        return response()->json(['error' => 'Invalid admin credentials'], 401);
    }


}
