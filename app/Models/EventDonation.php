<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventDonation extends Model
{
    protected $fillable = ['event_id', 'name', 'amount', 'attachment',];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}
