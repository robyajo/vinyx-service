<?php

namespace App\Services;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Models\User;

class JwtService
{
    protected string $secret;
    protected string $algorithm = 'HS256';

    public function __construct()
    {
        $this->secret = config('app.jwt_secret');
    }

    public function generateWsToken(User $user, int $ttl = 3600): string
    {
        $payload = [
            'sub' => (string) $user->id,
            'uuid' => $user->uuid,
            'name' => $user->name,
            'username' => $user->username,
            'iat' => time(),
            'exp' => time() + $ttl,
        ];

        return JWT::encode($payload, $this->secret, $this->algorithm);
    }

    public function verifyWsToken(string $token): ?\stdClass
    {
        try {
            return JWT::decode($token, new Key($this->secret, $this->algorithm));
        } catch (\Exception) {
            return null;
        }
    }
}
