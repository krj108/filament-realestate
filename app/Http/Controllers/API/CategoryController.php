<?php

namespace App\Http\Controllers\API;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;

class CategoryController extends Controller
{
    // استعراض جميع الأقسام مع الأقسام الفرعية
    public function index()
    {
        $categories = Category::with('children')->get();

        return response()->json([
            'status' => 'success',
            'data' => $categories
        ], Response::HTTP_OK);
    }

    // استعراض المقالات ضمن قسم معين عبر ID
    public function articles($id)
    {
        $category = Category::with(['children', 'articles' => function ($query) {
            $query->where('status', 'published')->latest();
        }])->findOrFail($id);

        return response()->json([
            'status' => 'success',
            'data' => $category
        ], Response::HTTP_OK);
    }
}
