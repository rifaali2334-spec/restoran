<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\News;

class NewsController extends Controller
{
    public function index()
    {
        $news = News::where('is_published', true)
            ->orderBy('created_at', 'desc')
            ->get();
        
        return response()->json(['data' => $news]);
    }

    public function show($id)
    {
        $news = News::where('is_published', true)->findOrFail($id);
        return response()->json(['data' => $news]);
    }
}
