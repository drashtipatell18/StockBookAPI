<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    public function category()
    {
        $categories = Category::all();
        return response()->json([
            'success' => true,
            'message' => 'Categories data successfully',
            'result' => $categories
        ], 200);
    }

    public function storeCategory(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category_name' => 'required|alpha',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 400);
        }

        $category = Category::create([
            'category_name' => $request->input('category_name'),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Category created successfully.',
            'result' => $category
        ], 201);
    }

    public function categoryUpdate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:categories,id',
            'category_name' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 400);
        }

        $category = Category::find($request->input('id'));
        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found'
            ], 404);
        }

        $category->update([
            'id' => $request->input('id'),
            'category_name' => $request->input('category_name')
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Category updated successfully.',
            'result' => $category
        ], 200);
    }

    public function categoryDestroy(Request $request)
    {
        $category = Category::find($request->input('id'));
        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found'
            ], 404);
        }

        $category->delete();
        return response()->json([
            'success' => true,
            'message' => 'Category deleted successfully.',
            'result' => $category
        ], 200);
    }
}
