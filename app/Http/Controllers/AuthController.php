<?php

namespace App\Http\Controllers;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function adminLogin(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::guard('web')->attempt($credentials)) {
            $user = Auth::user();

            // Check if the user has the 'admin' role
            if ($user->role == 'admin') {
                $token = $user->createToken('admin_auth_token')->plainTextToken;

                return response()->json(['token' => $token], 200);
            }
        }

        return response()->json(['error' => 'Invalid admin credentials'], 401);
    }

    public function userLogin(Request $request)
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
