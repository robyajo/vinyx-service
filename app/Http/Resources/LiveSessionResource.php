<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LiveSessionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => (string) $this->id,
            'accountId' => (string) $this->account_id,
            'platform' => $this->platform ?? 'tiktok',
            'status' => $this->status,
            'startedAt' => $this->started_at?->toIso8601String(),
            'endedAt' => $this->ended_at?->toIso8601String(),
            'metadata' => $this->metadata,
            'createdAt' => $this->created_at->toIso8601String(),
            'updatedAt' => $this->updated_at->toIso8601String(),
            'liveEvents' => LiveEventResource::collection($this->whenLoaded('liveEvents')),
            '_count' => $this->when($this->live_events_count !== null, [
                'liveEvents' => (int) $this->live_events_count,
            ]),
        ];
    }
}
