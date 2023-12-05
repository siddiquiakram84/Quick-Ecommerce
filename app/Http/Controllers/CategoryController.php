<?php

namespace App\Http\Controllers;

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
        $category = Category::findOrFail($id);
        $resp['success'] = true;
        $resp['data'] = $category;
        return response()->json($resp, 200);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

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
        $resp['success'] = true;
        return response()->json($resp, 204);
    }
}
