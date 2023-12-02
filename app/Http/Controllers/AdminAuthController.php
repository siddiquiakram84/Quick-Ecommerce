<?php

namespace App\Http\Controllers;

use App\Models\User;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class AdminAuthController extends Controller
{
    public function register(Request $request)
{
    try {
        $validatedData = $request->validate([
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

        // Check if the user already exists
        $existingUser = User::where('email', $validatedData['email'])->first();
        if ($existingUser) {
            return response()->json(['error' => 'User with this email already exists.', 'user' => $existingUser], 422);
        }

        // If the user doesn't exist, proceed with registration
        $newUser = User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
            'phone' => $validatedData['phone'],
            'role' => $validatedData['role'],
            'status' => $validatedData['status'],
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

            // Check if the user has the 'admin' role    "'"
            if ($user->role == 'admin') {
                $token = $user->createToken('admin_auth_token')->plainTextToken;

                return response()->json(['token' => $token], 200);
            }
        }

        return response()->json(['error' => 'Invalid admin credentials'], 401);
    }


}
