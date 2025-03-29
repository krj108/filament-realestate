<?php

namespace App\Http\Controllers\API;

use App\Models\Property;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;

class PropertyController extends Controller
{
    public function index()
    {
        $properties = Property::where('status', 'published')
            ->with('user') 
            ->latest()
            ->paginate(10);

        return response()->json([
            'status' => 'success',
            'data' => $properties
        ], Response::HTTP_OK);
    }

    public function show($slug)
    {
        $property = Property::where('slug', $slug)
            ->where('status', 'published')
            ->with('user')
            ->firstOrFail();

        return response()->json([
            'status' => 'success',
            'data' => $property
        ], Response::HTTP_OK);
    }
}
