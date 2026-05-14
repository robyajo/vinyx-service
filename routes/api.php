<?php

use App\Ai\Agents\ChatAgent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/ai-test', function (Request $request) {
    $prompt = $request->input('prompt');
    $response = (new ChatAgent)->stream($prompt);
    return $response;
    // return response()->json([
    //     'response' => $response,
    // ]);
});
