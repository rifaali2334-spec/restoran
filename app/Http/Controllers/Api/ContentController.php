<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Home;
use App\Models\Tentang;
use App\Models\ContactMessage;
use Illuminate\Http\Request;

class ContentController extends Controller
{
    public function home()
    {
        $home = Home::first();
        return response()->json(['data' => $home]);
    }

    public function about()
    {
        $about = Tentang::first();
        return response()->json(['data' => $about]);
    }

    public function contact(Request $request)
    {
        $message = ContactMessage::create([
            'name' => $request->name,
            'email' => $request->email,
            'message' => $request->message,
        ]);

        return response()->json(['message' => 'Message sent successfully', 'data' => $message], 201);
    }
}
