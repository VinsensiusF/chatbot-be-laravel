<?php

namespace App\Http\Controllers;

use App\Models\Chatbot;
use Illuminate\Http\Request;

class ChatbotController extends Controller
{
    // Handle GET request
    public function index()
    {
        return response()->json(Chatbot::all());
    }

    // Handle POST request
    public function store(Request $request)
    {
        $request->validate([
            'message' => 'required|string',
        ]);

        $chatbot = Chatbot::create([
            'message' => $request->message,
        ]);

        return response()->json($chatbot, 201);
    }
}