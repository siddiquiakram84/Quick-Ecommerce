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
    public function register(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string',
                'email' => [
                    'required',
                    'email',
                    Rule::unique('users'),
                ],
                'password' => 'required|min:6',
                'phone' => 'nullable|string|max:20',
                'role' => 'required|string',
                'status' => 'required|integer',
            ]);

            // Find user by email
            $user = User::where('email', $request->email)->first();

            // Check if the user already exists
            if ($user) {
                return response()->json(['error' => 'User with this email already exists.', 'user' => $user], 422);
            }

            // If the user doesn't exist, proceed with registration
            $newUser = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'phone' => $request->phone,
                'role' => $request->role,
                'status' => $request->status,
            ]);

            return response()->json(['user' => $newUser, 'message' => 'User registered successfully.'], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error during user registration.', 'message' => $e->getMessage()], 500);
        }
    }
    
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::guard('web')->attempt($credentials)) {
            $user = Auth::user();
            $token = $user->createToken('user_auth_token')->plainTextToken;

            return response()->json(['token' => $token], 200);
        }

        return response()->json(['error' => 'Invalid credentials'], 401);
    }

}
