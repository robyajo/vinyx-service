<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class AvatarService
{
    public function ensureLocalAvatar(User $user): void
    {
        $avatar = $user->avatar;

        if (!$avatar || !str_starts_with($avatar, 'http')) {
            return;
        }

        $ext = $this->guessExtension($avatar);
        $filename = "avatars/{$user->id}.{$ext}";

        if (Storage::disk('public')->exists($filename)) {
            if ($avatar !== $this->localUrl($filename)) {
                $user->updateQuietly(['avatar' => $this->localUrl($filename)]);
            }
            return;
        }

        try {
            $response = Http::timeout(5)->get($avatar);

            if ($response->successful()) {
                Storage::disk('public')->put($filename, $response->body());
                $user->updateQuietly(['avatar' => $this->localUrl($filename)]);
            }
        } catch (ConnectionException) {
            // Silently fail — keep the original Google URL as fallback
        }
    }

    private function guessExtension(string $url): string
    {
        if (preg_match('/\.(jpe?g|png|gif|webp)(?:[?#]|$)/i', $url, $m)) {
            return strtolower($m[1]);
        }
        return 'jpg';
    }

    private function localUrl(string $filename): string
    {
        $base = rtrim(config('app.url'), '/');
        return "{$base}/storage/{$filename}";
    }
}
