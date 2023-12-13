<?php

namespace App\Http\Controllers;

use App\Models\OrderProduct;
use Illuminate\Http\Request;

class OrderProductController extends Controller
{
    public function index()
    {
        $orderProducts = OrderProduct::all();
        return response()->json($orderProducts, 200);
    }
}
