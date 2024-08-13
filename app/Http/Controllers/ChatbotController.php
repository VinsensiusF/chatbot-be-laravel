<?php

namespace App\Http\Controllers;

use App\Models\Chatbot;
use Illuminate\Http\Request;
use GuzzleHttp\Client;

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
        // Step 1: Validate the user's input
        $request->validate([
            'message' => 'required|string',
        ]);

        // Step 2: Save the user's message to the database
        $userMessage = Chatbot::create([
            'message' => $request->message,
        ]);

        // Step 3: Send the message to the OpenAI API and get a response
        $client = new Client();
        $response = $client->post('https://api.openai.com/v1/completions', [
            'headers' => [
                'Authorization' => 'Bearer ' . env('OPENAI_API_KEY'),
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'model' => 'gpt-3.5-turbo-instruct',
                'prompt' => $request->message,
                'max_tokens' => 150,
            ],
        ]);

        $responseBody = json_decode($response->getBody()->getContents(), true);
        $openAIAnswer = $responseBody['choices'][0]['text'] ?? 'Sorry, I did not understand that.';

        // Step 4: Save the OpenAI response to the database
        $botResponse = Chatbot::create([
            'message' => $openAIAnswer,
        ]);

        // Step 5: Return both the user's message and the OpenAI response
        return response()->json([
            'user_message' => $userMessage,
            'bot_response' => $botResponse,
        ], 201);
    }
}