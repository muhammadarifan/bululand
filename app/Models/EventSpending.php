<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventSpending extends Model
{
    protected $fillable = ['event_id', 'description', 'amount', 'attachment',];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}
