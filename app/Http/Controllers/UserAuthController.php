<?php

namespace App\Http\Controllers;

use App\Models\User;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserAuthController extends Controller
{
    public function register(Request $request){

        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'phone' => 'nullable|string|max:20',
            'role' => 'required|string',
            'status' => 'required|integer',
        ]);

        // If the user doesn't exist, proceed with registration
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'role' => $request->role,
            'status' => $request->status,
        ]);

        // return response()->json(['user' => $user, 'message' => 'User registered successfully.'], 201);
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

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::guard('web')->attempt($credentials)) {
            $user = Auth::user();
            $token = $user->createToken('user_auth_token')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Login successful',
                'token' => $token,
                'user' => [
                    'id' => $user->id,
                    'username' => $user->name,
                    'email' => $user->email,
                ],
            ]);
        }

        return response()->json(['error' => 'Invalid credentials'], 401);
    }

}
