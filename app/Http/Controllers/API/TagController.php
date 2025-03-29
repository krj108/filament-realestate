<?php

namespace App\Http\Controllers\API;

use App\Models\Tag;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;

class TagController extends Controller
{
   
    public function index()
    {
        $tags = Tag::latest()->paginate(10);

        return response()->json([
            'status' => 'success',
            'data' => $tags
        ], Response::HTTP_OK);
    }

  
    public function show($id)
    {
        $tag = Tag::findOrFail($id);

        return response()->json([
            'status' => 'success',
            'data' => $tag
        ], Response::HTTP_OK);
    }
}
