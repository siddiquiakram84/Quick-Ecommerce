<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Models\User;
use App\Enums\UserRoleEnums;

class DashboardController extends Controller
{

    public function index()
    {
        $role = UserRoleEnums::USER;
        $products = Product::latest()->count();
        $categories = Category::latest()->count();
        $userCount = User::where('role', $role)->count();

        // Standardized response format
        $response = [
            'status' => 'success',
            'message' => 'Data retrieved successfully',
            'data' => [
                'products' => $products,
                'categories' => $categories,
                'user_count' => $userCount,
            ],
        ];

        // Return data as JSON response with a 200 status code
        return response()->json($response, 200);
    }
}
