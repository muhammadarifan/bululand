<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GowaWebhookEvent extends Model
{
    protected $fillable = [
        'event_id',
        'payload',
        'processed_at',
    ];

    protected $casts = [
        'payload' => 'array',
        'processed_at' => 'datetime',
    ];
}
