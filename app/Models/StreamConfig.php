<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StreamConfig extends Model
{
    protected $fillable = [
        'user_id',
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
