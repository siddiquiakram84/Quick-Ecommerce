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

        // Return data as JSON response
        return response()->json([$products, $categories
        ], 200);
    }
}
