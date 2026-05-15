<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use App\Services\JwtService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthTokenController extends Controller
{
    use ApiResponse;

    public function __construct(
        protected JwtService $jwtService,
    ) {}

    public function wsToken(Request $request): JsonResponse
    {
        $user = $request->user();

        $token = $this->jwtService->generateWsToken($user);

        return $this->successResponse([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => 3600,
        ], 'WebSocket token generated');
    }

    public function verifyToken(Request $request): JsonResponse
    {
        $token = $request->bearerToken();

        if (!$token) {
            return $this->unauthorizedResponse('Token tidak disertakan');
        }

        $decoded = $this->jwtService->verifyWsToken($token);

        if (!$decoded) {
            return $this->unauthorizedResponse('Token tidak valid atau kadaluarsa');
        }

        return $this->successResponse([
            'valid' => true,
            'user' => [
                'id' => $decoded->sub,
                'uuid' => $decoded->uuid ?? null,
                'name' => $decoded->name ?? null,
                'username' => $decoded->username ?? null,
            ],
        ], 'Token valid');
    }
}
