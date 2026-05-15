<?php

use App\Ai\Agents\ChatAgent;
use App\Http\Controllers\Api\AuthTokenController;
use App\Http\Controllers\Api\LiveActivityController;
use App\Http\Controllers\Api\StreamConfigController;
use App\Http\Controllers\Api\TikTokAccountController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PermissionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::post('/ai-test', function (Request $request) {
    $prompt = $request->input('prompt');
    $response = (new ChatAgent)->stream($prompt);
    return $response;
});

/*
|--------------------------------------------------------------------------
| Listener Service Routes (for old Node.js listener-service)
|--------------------------------------------------------------------------
| Endpoint ini public karena listener-service tidak membawa token.
| TODO: tambahkan API key validation untuk production.
*/

Route::get('/stream-config', [StreamConfigController::class, 'publicConfig']);
Route::post('/stream-config', [StreamConfigController::class, 'update']);
Route::put('/stream-config', [StreamConfigController::class, 'update']);

Route::post('/live-activities', [LiveActivityController::class, 'store']);

/*
|--------------------------------------------------------------------------
| v1 API
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {

    /*
    | Authentication
    */
    Route::prefix('auth')->group(function () {
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/login', [AuthController::class, 'login']);

        Route::middleware('auth:api')->group(function () {
            Route::get('/me', [AuthController::class, 'me']);
            Route::post('/refresh', [AuthController::class, 'refresh']);
            Route::post('/logout', [AuthController::class, 'logout']);

            // JWT for WebSocket (NestJS)
            Route::post('/ws-token', [AuthTokenController::class, 'wsToken']);
        });

        Route::get('/{provider}/redirect', [AuthController::class, 'socialRedirect']);
        Route::get('/{provider}/callback', [AuthController::class, 'socialCallback']);
    });

    /*
    | WebSocket Token Verification (no auth - uses Bearer token)
    */
    Route::post('/auth/verify-ws-token', [AuthTokenController::class, 'verifyToken']);

    /*
    | Protected Routes (Passport)
    */
    Route::middleware('auth:api')->group(function () {

        Route::prefix('management')->group(function () {
            Route::apiResource('roles', RoleController::class)->middleware('role:Super Admin');
            Route::apiResource('permissions', PermissionController::class)->middleware('role:Super Admin');
        });

        Route::get('/user', function (Request $request) {
            return $request->user();
        });

        /*
        | TikTok Accounts
        */
        Route::prefix('tiktok')->group(function () {
            Route::get('/accounts', [TikTokAccountController::class, 'index']);
            Route::post('/accounts', [TikTokAccountController::class, 'store']);
            Route::get('/accounts/{account}', [TikTokAccountController::class, 'show']);
            Route::patch('/accounts/{account}', [TikTokAccountController::class, 'update']);
            Route::delete('/accounts/{account}', [TikTokAccountController::class, 'destroy']);

            Route::get('/accounts/{account}/sessions', [TikTokAccountController::class, 'sessions']);
            Route::get('/sessions/{session}', [TikTokAccountController::class, 'sessionDetail']);
            Route::get('/sessions/{session}/events', [TikTokAccountController::class, 'sessionEvents']);
        });

        /*
        | Stream Config
        */
        Route::get('/stream-config', [StreamConfigController::class, 'show']);
        Route::put('/stream-config', [StreamConfigController::class, 'update']);
    });
});
