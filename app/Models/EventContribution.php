<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventContribution extends Model
{
    protected $fillable = [
        'event_id',
        'house_id',
        'amount',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function house()
    {
        return $this->belongsTo(House::class);
    }
}
