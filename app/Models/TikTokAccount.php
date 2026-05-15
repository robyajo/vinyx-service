<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TikTokAccount extends Model
{
    protected $fillable = [
        'user_id',
        'username',
        'unique_id',
        'avatar_url',
        'proxy',
        'is_connected',
    ];

    protected function casts(): array
    {
        return [
            'is_connected' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function liveSessions(): HasMany
    {
        return $this->hasMany(LiveSession::class, 'account_id');
    }
}
