<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use App\Models\StreamConfig;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class StreamConfigController extends Controller
{
    use ApiResponse;

    public function show(Request $request): JsonResponse
    {
        $config = StreamConfig::firstOrCreate(
            ['user_id' => $request->user()->id],
            [
                'tiktok_username' => null,
                'listener_port' => 9090,
                'is_active' => false,
                'new_user_sound' => '/storage/sounds/new-user.mp3',
                'new_user_sound_enabled' => true,
                'chat_sound' => '/storage/sounds/chat.mp3',
                'chat_sound_enabled' => true,
                'tts_active' => true,
                'tts_voice' => 'female',
            ]
        );

        return $this->successResponse($config, 'Konfigurasi stream');
    }

    public function update(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'tiktok_username' => 'nullable|string|max:100',
            'listener_port' => 'nullable|integer|min:1024|max:65535',
            'is_active' => 'boolean',
            'new_user_sound' => 'nullable|string',
            'new_user_sound_enabled' => 'boolean',
            'chat_sound' => 'nullable|string',
            'chat_sound_enabled' => 'boolean',
            'tts_active' => 'boolean',
            'tts_voice' => 'in:female,male',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator->errors());
        }

        $config = StreamConfig::updateOrCreate(
            ['user_id' => $request->user()->id],
            $validator->validated(),
        );

        return $this->successResponse($config, 'Konfigurasi stream diperbarui');
    }

    public function publicConfig(Request $request): JsonResponse
    {
        $apiKey = $request->header('X-API-Key') ?? $request->input('api_key');

        if ($apiKey && $apiKey === config('app.listener_api_key')) {
            // Authenticated request via API key - return first active config
            $config = StreamConfig::where('is_active', true)->first();
            if ($config) {
                return response()->json($config->toArray());
            }
        }

        // Backward compatibility: return first available config
        $config = StreamConfig::first();

        return response()->json(
            $config?->toArray() ?? [
                'tiktok_username' => '',
                'listener_port' => 9090,
                'is_active' => false,
                'new_user_sound' => '/storage/sounds/new-user.mp3',
                'new_user_sound_enabled' => true,
                'chat_sound' => '/storage/sounds/chat.mp3',
                'chat_sound_enabled' => true,
                'tts_active' => true,
                'tts_voice' => 'female',
            ],
        );
    }
}
