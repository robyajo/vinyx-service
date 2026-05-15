<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StreamConfig extends Model
{
    protected $fillable = [
        'user_id',
        'active_platform',
        'alert_overlay_enabled',
        'member_ticker_enabled',
        'gift_overlay_enabled',
        'tiktok_username',
        'listener_port',
        'is_active',
        'new_user_sound',
        'new_user_sound_enabled',
        'chat_sound',
        'chat_sound_enabled',
        'tts_active',
        'tts_voice',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'alert_overlay_enabled' => 'boolean',
            'member_ticker_enabled' => 'boolean',
            'gift_overlay_enabled' => 'boolean',
            'new_user_sound_enabled' => 'boolean',
            'chat_sound_enabled' => 'boolean',
            'tts_active' => 'boolean',
            'listener_port' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
