<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::all();
        return response()->json([
            'success' => true,
            'data' => $products,
        ], 200);
    }

    public function show($id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found',
            ], 404);
        }
        
        return response()->json([
            'success' => true,
            'data' => $product,
        ], 200);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric',
            'quantity_in_stock' => 'required|integer',
            'image' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Check if an image file is provided
        if ($request->hasFile('image')) {
            // Upload the image
            $imagePath = $request->file('image')->store('product_images', 'public');

            // Add the image path to the validated data
            $validatedData['image'] = $imagePath;
        }

        // Create the product
        $product = Product::create($validatedData);

        return response()->json([
            'success' => true,
            'data' => $product,
            'message' => 'Product created successfully',
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found',
            ], 404);
        }

        $validatedData = $request->validate([
            'category_id' => 'exists:categories,id',
            'name' => 'string|max:255',
            'description' => 'string',
            'price' => 'numeric',
            'quantity_in_stock' => 'integer',
            'image' => 'nullable|string',
        ]);

        $product->update($validatedData);

        return response()->json([
            'success' => true,
            'data' => $product,
            'message' => 'Product updated successfully',
        ], 200);
    }

    public function destroy($id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found',
            ], 404);
        }

        $product->delete();

        return response()->json([
            'success' => true,
            'message' => 'Product deleted successfully',
        ], 204);
    }

    public function search(Request $request)
    {
        $query = $request->input('query');

        // Perform a search query on your products table
        $results = Product::where('name', 'like', "%$query%")
            ->orWhere('description', 'like', "%$query%")->get();

        // Return the results as JSON
        $resp['message'] = true;
        $resp['data'] = $results;
        return response()->json($resp);
    }

}