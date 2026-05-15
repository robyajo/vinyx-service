<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LiveEvent extends Model
{
    protected $fillable = [
        'session_id',
        'type',
        'data',
        'raw',
        'timestamp',
    ];

    protected function casts(): array
    {
        return [
            'data' => 'array',
            'raw' => 'array',
            'timestamp' => 'datetime',
        ];
    }

    public function session(): BelongsTo
    {
        return $this->belongsTo(LiveSession::class, 'session_id');
    }
}
