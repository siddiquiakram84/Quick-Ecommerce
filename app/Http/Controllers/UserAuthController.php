<?php

namespace App\Http\Controllers;

use App\Enums\UserRoleEnums;
use App\Enums\UserStatusEnums;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class UserAuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'phone' => 'nullable|string|max:20',
        ]);

        // Set default role and status for regular users
        $request->merge(['role' => UserRoleEnums::USER, 'status' => UserStatusEnums::ACTIVE]);

        // Create user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'role' => $request->role,
            'status' => $request->status,
        ]);

        $token = $user->createToken('user_auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'User registered successfully',
            'token' => $token,
            'user' => $user,
        ]);
    }

    public function login(Request $request)
    {
        // Validate the incoming login credentials
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Attempt to authenticate the user
        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            // Check if the user status is active
            if ($user->status === UserStatusEnums::ACTIVE) {
                // User is active, generate an authentication token
                $token = $user->createToken('user_auth_token')->plainTextToken;

                // Return a JSON response with success details, token, and user information
                return response()->json([
                    'success' => true,
                    'message' => 'Login successful',
                    'token' => $token,
                    'user' => $user,
                ]);
            } 
            else {
                // User is inactive, log them out and return an error response
                Auth::logout();

                return response()->json([
                    'success' => false,
                    'message' => 'User is inactive. Login not allowed.',
                ], 401); // 401 Unauthorized status code
            }
        }

        // If authentication fails, throw a validation exception
        throw ValidationException::withMessages([
            'email' => ['Invalid credentials.'],
        ]);
    }

}
