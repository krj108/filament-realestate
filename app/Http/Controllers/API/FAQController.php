<?php

namespace App\Http\Controllers\API;

use App\Models\FAQ;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;

class FAQController extends Controller
{
    
    public function index()
    {
        $faqs = FAQ::latest()->get();

        return response()->json([
            'status' => 'success',
            'data' => $faqs
        ], Response::HTTP_OK);
    }

   
    public function show($id)
    {
        $faq = FAQ::findOrFail($id);

        return response()->json([
            'status' => 'success',
            'data' => $faq
        ], Response::HTTP_OK);
    }
}
