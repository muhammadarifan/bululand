<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = ['name'];

    public function contributions()
    {
        return $this->hasMany(EventContribution::class);
    }

    public function donations()
    {
        return $this->hasMany(EventDonation::class);
    }

    public function spendings()
    {
        return $this->hasMany(EventSpending::class);
    }
}
