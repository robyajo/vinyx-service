<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LiveSession extends Model
{
    protected $fillable = [
        'id',
        'account_id',
        'status',
        'started_at',
        'ended_at',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'ended_at' => 'datetime',
            'metadata' => 'array',
        ];
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(TikTokAccount::class, 'account_id');
    }

    public function liveEvents(): HasMany
    {
        return $this->hasMany(LiveEvent::class, 'session_id');
    }
}
