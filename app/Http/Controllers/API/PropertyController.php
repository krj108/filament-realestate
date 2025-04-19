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
            ->with(['user', 'governorate', 'city'])
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
            ->with(['user', 'governorate', 'city'])
            ->firstOrFail();

        return response()->json([
            'status' => 'success',
            'data' => $property
        ], Response::HTTP_OK);
    }

   
    public function byGovernorate($governorateId)
    {
        $properties = Property::where('governorate_id', $governorateId)
            ->where('status', 'published')
            ->with(['user', 'governorate', 'city'])
            ->latest()
            ->paginate(10);

        return response()->json([
            'status' => 'success',
            'data' => $properties
        ], Response::HTTP_OK);
    }

   
    public function byCity($cityId)
    {
        $properties = Property::where('city_id', $cityId)
            ->where('status', 'published')
            ->with(['user', 'governorate', 'city'])
            ->latest()
            ->paginate(10);

        return response()->json([
            'status' => 'success',
            'data' => $properties
        ], Response::HTTP_OK);
    }
}
