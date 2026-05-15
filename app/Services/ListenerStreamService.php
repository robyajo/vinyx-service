<?php

namespace App\Services;

use App\Models\TikTokAccount;
use Illuminate\Support\Facades\Http;

class ListenerStreamService
{
    protected string $baseUrl;
    protected string $apiKey;

    public function __construct()
    {
        $this->baseUrl = config('app.listener_stream_url');
        $this->apiKey = config('app.listener_api_key');
    }

    public function connect(string $accountId, string $userId): array
    {
        $account = TikTokAccount::find($accountId);

        $payload = [
            'accountId' => $accountId,
            'userId' => $userId,
            'api_key' => $this->apiKey,
        ];

        if ($account) {
            $payload['username'] = $account->username;
            $payload['uniqueId'] = $account->unique_id;
            $payload['avatarUrl'] = $account->avatar_url;
            $payload['proxy'] = $account->proxy;
        }

        try {
            $response = Http::timeout(15)->post("{$this->baseUrl}/api/internal/tiktok/connect", $payload);
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            return [
                'success' => false,
                'error' => 'Tidak dapat terhubung ke service stream. Pastikan listener-stream berjalan.',
            ];
        }

        if ($response->successful()) {
            return [
                'success' => true,
                'session' => $response->json(),
            ];
        }

        $body = $response->json();
        $reason = $body['message'] ?? $response->body();

        \Log::error('ListenerStream connect failed', [
            'accountId' => $accountId,
            'status' => $response->status(),
            'reason' => $reason,
        ]);

        return [
            'success' => false,
            'error' => $reason,
        ];
    }

    public function disconnect(string $accountId, string $userId): ?array
    {
        $response = Http::timeout(10)->post("{$this->baseUrl}/api/internal/tiktok/disconnect", [
            'accountId' => $accountId,
            'userId' => $userId,
            'api_key' => $this->apiKey,
        ]);

        if ($response->successful()) {
            return $response->json();
        }

        \Log::error('ListenerStream disconnect failed', [
            'accountId' => $accountId,
            'status' => $response->status(),
            'body' => $response->body(),
        ]);

        return null;
    }
}
