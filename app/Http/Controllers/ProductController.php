<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::all();
        return response()->json($products, 200);
    }

    public function show($id)
    {
        $product = Product::findOrFail($id);
        return response()->json($product, 200);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric',
            'quantity_in_stock' => 'required|integer',
            'image' => 'nullable|string',
        ]);

        $product = Product::create($validatedData);

        return response()->json($product, 201);
    }

    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $validatedData = $request->validate([
            'category_id' => 'exists:categories,id',
            'name' => 'string|max:255',
            'description' => 'string',
            'price' => 'numeric',
            'quantity_in_stock' => 'integer',
            'image' => 'nullable|string',
        ]);

        $product->update($validatedData);

        return response()->json($product, 200);
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();

        return response()->json(null, 204);
    }
}
