<?php

namespace App\Http\Controllers\API;

use App\Models\Article;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ArticleController extends Controller
{
    public function index()
    {
        $articles = Article::where('status', 'published')
            ->with(['category', 'user', 'tags'])
            ->latest()
            ->paginate(6);

        return response()->json([
            'status' => 'success',
            'data' => $articles
        ], Response::HTTP_OK);
    }

    public function show($slug)
    {
        $article = Article::where('slug', $slug)
            ->where('status', 'published')
            ->with(['category', 'user', 'tags'])
            ->firstOrFail();

        return response()->json([
            'status' => 'success',
            'data' => $article
        ], Response::HTTP_OK);
    }
}
