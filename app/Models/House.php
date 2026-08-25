<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class House extends Model
{
    protected $fillable = [
        'code',
        'name',
    ];

    public function eventContributions()
    {
        return $this->hasMany(EventContribution::class);
    }
}
