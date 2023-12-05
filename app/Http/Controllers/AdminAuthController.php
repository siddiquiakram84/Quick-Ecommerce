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
//     public function register(Request $request)
// {
//         $validatedData = $request->validate([
//             'name' => 'required|string',
//             'email' => [
//                 'required',
//                 'email',
//                 'unique:users'
//             ],
//             'password' => 'required|min:6',
//             'phone' => 'nullable|string|max:20',
//             'role' => 'required|string',
//             'status' => 'required|integer',
//         ]);

//         // If the user doesn't exist, proceed with registration
//         $newUser = User::create([
//             'name' => $validatedData['name'],
//             'email' => $validatedData['email'],
//             'password' => Hash::make($validatedData['password']),
//             'phone' => $validatedData['phone'],
//             'role' => $validatedData['role'],
//             'status' => $validatedData['status'],
//         ]);

//         return response()->json(['user' => $newUser, 'message' => 'User registered successfully.'], 201);
// }
    
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::guard('web')->attempt($credentials)) {
            $user = Auth::user();

            // Check if the user has the 'admin' role
            if ($user->role == 'admin') {
                $token = $user->createToken('admin_auth_token')->plainTextToken;

                // return response()->json(['token' => $token], 200);
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
