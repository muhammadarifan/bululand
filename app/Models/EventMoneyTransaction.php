<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventMoneyTransaction extends Model
{
    protected $fillable = [
        'event_id',
        'house_id',
        'description',
        'type',
        'category',
        'amount',
        'attachment',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function house(): BelongsTo
    {
        return $this->belongsTo(House::class);
    }
}
