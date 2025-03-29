<?php

namespace App\Http\Controllers\API;

use App\Models\Page;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;

class PageController extends Controller
{
    
    public function index()
    {
        $pages = Page::where('is_published', 1)
            ->with('user:id,name') 
            ->latest()
            ->paginate(10);

        return response()->json([
            'status' => 'success',
            'data' => $pages
        ], Response::HTTP_OK);
    }

   
    public function show($slug)
    {
        $page = Page::where('slug', $slug)
            ->where('is_published', 1)
            ->with('user:id,name')
            ->firstOrFail();

        return response()->json([
            'status' => 'success',
            'data' => $page
        ], Response::HTTP_OK);
    }
}
