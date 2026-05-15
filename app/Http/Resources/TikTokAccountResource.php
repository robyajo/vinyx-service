<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TikTokAccountResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => (string) $this->id,
            'userId' => (string) $this->user_id,
            'username' => $this->username,
            'uniqueId' => $this->unique_id,
            'avatarUrl' => $this->avatar_url,
            'isConnected' => (bool) $this->is_connected,
            'proxy' => $this->proxy,
            'createdAt' => $this->created_at->toIso8601String(),
            'updatedAt' => $this->updated_at->toIso8601String(),
            'liveSessions' => LiveSessionResource::collection($this->whenLoaded('liveSessions')),
            '_count' => $this->when($this->resource->relationLoaded('liveSessions') || $this->live_sessions_count !== null, [
                'liveSessions' => (int) ($this->live_sessions_count ?? $this->liveSessions->count()),
            ]),
        ];
    }
}
