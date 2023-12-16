<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::all();
        $resp['success'] = true;
        $resp['data'] = $categories;
        return response()->json($resp, 200);
    }

    public function show($id)
    {
        $products = Product::where('category_id', $id)->get();
        $resp['success'] = true;
        $resp['data'] = $products;

        return response()->json($resp, 200);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Check if an image file is provided
        if ($request->hasFile('image')) {
            // Upload the image
            $imagePath = $request->file('image')->store('category_images', 'public');

            // Add the image path to the validated data
            $validatedData['image'] = $imagePath;
        }

        $category = Category::create($validatedData);

        $resp['success'] = true;
        $resp['data'] = $category;
        return response()->json($resp, 200);
    }

    public function update(Request $request, $id)
    {
        $category = Category::findOrFail($id);

        $validatedData = $request->validate([
            'name' => 'string|max:255',
            'description' => 'nullable|string',
        ]);

        $category->update($validatedData);

        $resp['success'] = true;
        $resp['data'] = $category;
        return response()->json($resp, 200);
    }

    public function destroy($id)
    {
        $category = Category::findOrFail($id);
        $category->delete();

        return response()->json(null, 204);
    }

    public function listCategories()
    {
        $categories = Category::all();

        return response()->json(['categories' => $categories], 200);
    }

}
