<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LiveEventResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => (string) $this->id,
            'sessionId' => (string) $this->session_id,
            'type' => $this->type,
            'data' => $this->data,
            'raw' => $this->raw,
            'timestamp' => $this->timestamp?->toIso8601String(),
            'createdAt' => $this->created_at->toIso8601String(),
        ];
    }
}
