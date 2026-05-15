<?php

use App\Ai\Agents\ChatAgent;
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


Route::prefix('v1')->group(function () {

    Route::prefix('auth')->group(function () {
        // Credential Auth
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/login', [AuthController::class, 'login']);
        Route::middleware('auth:api')->group(function () {
            Route::get('/me', [AuthController::class, 'me']);
            Route::post('/refresh', [AuthController::class, 'refresh']);
            Route::post('/logout', [AuthController::class, 'logout']);
        });


        // Social Auth
        Route::get('/{provider}/redirect', [AuthController::class, 'socialRedirect']);
        Route::get('/{provider}/callback', [AuthController::class, 'socialCallback']);
    });

    /*
    |--------------------------------------------------------------------------
    | Protected Routes (Passport)
    |--------------------------------------------------------------------------
    */

    Route::middleware('auth:api')->group(function () {

        Route::prefix('v1/management')->group(function () {
            Route::apiResource('roles', RoleController::class)->middleware('role:Super Admin');
            Route::apiResource('permissions', PermissionController::class)->middleware('role:Super Admin');
        });

        // Legacy / Debug
        Route::get('/user', function (Request $request) {
            return $request->user();
        });
    });
});
