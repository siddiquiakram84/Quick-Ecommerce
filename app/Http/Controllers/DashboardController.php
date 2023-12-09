<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Models\User;

class DashboardController extends Controller
{

    public function index()
    {
        $products = Product::all();
        $categories = Category::all();
        $userCount = User::count(); // Example: Count of users

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
