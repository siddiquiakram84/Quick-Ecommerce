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
        // Trim whitespace from the beginning and end
        $query = trim($request->input('query'));

        // Check if the query is not empty after trimming
        if ($query !== '') {
            $results = Product::where(function ($queryBuilder) use ($query) {
            $queryBuilder->where('name', 'like', "%$query%")
                ->orWhere('description', 'like', "%$query%")
                ->orWhereHas('category', function ($categoryQuery) use ($query) {
                    $categoryQuery->where('name', 'like', "%$query%")
                        ->orWhere('description', 'like', "%$query%");
                });
            })
            ->get();

            // Check if any results are found
            if ($results->isEmpty()) {
                // No products found
                $resp['message'] = 'No products found.';
                $resp['data'] = [];
            } else {
                // Products found
                $resp['message'] = 'Products found.';
                $resp['data'] = $results;
            }
        } else {
            // Empty query
            $resp['message'] = 'Empty query.';
            $resp['data'] = [];
        }

        // Return the results as JSON
        return response()->json($resp);
    }

}