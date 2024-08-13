<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChatbotController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

// Define your API routes here
Route::post('/chatbot', [ChatbotController::class, 'store']);
Route::get('/chatbot', [ChatbotController::class, 'index']);
Route::get('/chatbots/session/{id}', [ChatbotController::class, 'getSession']);