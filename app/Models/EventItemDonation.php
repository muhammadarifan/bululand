<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventItemDonation extends Model
{
    protected $fillable = [
        'event_id',
        'house_id',
        'donor_name',
        'is_anonymous',
        'item_name',
        'quantity',
        'unit',
        'description',
        'attachment',
    ];

    protected $casts = [
        'is_anonymous' => 'boolean',
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
