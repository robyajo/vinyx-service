<?php

use App\Ai\Agents\ChatAgent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Laravel\Socialite\Socialite;

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

Route::prefix('v1/auth')->group(function () {
    Route::get('/{provider}/redirect', function ($provider) {
        return Socialite::driver($provider)->redirect();
    });

    Route::get('/{provider}/callback', function ($provider) {
        $user = Socialite::driver($provider)->user();
        return $user->token;
    });
});
