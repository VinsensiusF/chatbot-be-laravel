<?php

namespace App\Http\Controllers;

use App\Models\Chatbot;
use Illuminate\Http\Request;
use App\Models\Message;
use App\Models\ChatSession;

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
            'chat_session_id' => 'nullable|exists:chat_sessions,id',  // Validate session ID
        ]);

        // Step 2: Create or find the session
        $session = $request->chat_session_id 
            ? ChatSession::find($request->chat_session_id) 
            : ChatSession::create(['user_id' => $request->user()->id ?? null]);

        // Step 3: Save the user's message
        $userMessage = Message::create([
            'chat_session_id' => $session->id,
            'sender' => 'user',
            'message' => $request->message,
        ]);

        // Step 4: Generate a bot response
        $responseMessage = $this->getReply($request->message);

        // Step 5: Save the bot's response
        $botMessage = Message::create([
            'chat_session_id' => $session->id,
            'sender' => 'bot',
            'message' => $responseMessage,
        ]);

        // Step 6: Return the session ID and the conversation history
        return response()->json([
            'chat_session_id' => $session->id,
            'messages' => $session->messages,
        ], 201);
    }

    private function getReply($message)
    {
        $message = strtolower($message);

        if (strpos($message, 'hello') !== false || strpos($message, 'hi') !== false) {
            return 'Hello! How can I assist you today?';
        } elseif (strpos($message, 'how are you') !== false) {
            return 'I am just a program, but I am here to help you!';
        } elseif (strpos($message, 'your name') !== false) {
            return 'I am your virtual assistant. How can I assist you today?';
        } elseif (strpos($message, 'time') !== false) {
            date_default_timezone_set('Asia/Jakarta');
            return 'The current time is ' . date('H:i') . '.';
        } elseif (strpos($message, 'date') !== false) {
            return 'Today is ' . date('Y-m-d') . '.';
        } elseif (strpos($message, 'day of the week') !== false) {
            return 'Today is ' . date('l') . '.';
        } elseif (strpos($message, 'latitude') !== false) {
            // Assuming you have access to the latitude; for example, use a placeholder
            $latitude = '52.520'; // Example latitude
            return 'Your current latitude is ' . $latitude . '.';
        } elseif (strpos($message, 'longitude') !== false) {
            // Assuming you have access to the longitude; for example, use a placeholder
            $longitude = '13.405'; // Example longitude
            return 'Your current longitude is ' . $longitude . '.';
        } elseif (strpos($message, 'weather') !== false) {
            // You could integrate with a weather API, but here's a placeholder response
            return 'The current weather is sunny with a temperature of 25°C.';
        } elseif (strpos($message, 'random number') !== false) {
            return 'Here is a random number: ' . rand(1, 100) . '.';
        } elseif (strpos($message, 'current month') !== false) {
            return 'The current month is ' . date('F') . '.';
        } elseif (strpos($message, 'year') !== false) {
            return 'The current year is ' . date('Y') . '.';
        } else {
            return 'Sorry, I did not understand that. Can you please rephrase?';
        }
    }

    public function getSession(Request $request, $id)
    {
        // Step 7: Retrieve the session and its messages
        $session = ChatSession::with('messages')->findOrFail($id);

        return response()->json([
            'session_id' => $session->id,
            'messages' => $session->messages,
        ]);
    }
}