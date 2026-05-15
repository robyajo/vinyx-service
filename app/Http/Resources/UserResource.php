<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => (string) $this->id,
            'username' => $this->username,
            'email' => $this->email,
            'displayName' => $this->name,
            'avatarUrl' => $this->avatar,
            'bio' => null,
            'whatsapp' => null,
            'instagram' => null,
            'tiktok' => null,
            'youtube' => null,
            'website' => null,
            'role' => $this->roles->pluck('name')->implode(','),
            'provider' => $this->provider_name ?? 'email',
            'isActive' => $this->is_active === 'active',
            'gems' => (int) ($this->gems ?? 0),
            'eventsUsed' => (int) ($this->events_used ?? 0),
            'eventsCycleAt' => $this->events_cycle_at?->toIso8601String() ?? '',
            'emailVerified' => !is_null($this->email_verified_at),
            'emailVerifiedAt' => $this->email_verified_at?->toIso8601String(),
            'createdAt' => $this->created_at->toIso8601String(),
            'updatedAt' => $this->updated_at->toIso8601String(),
        ];
    }
}
