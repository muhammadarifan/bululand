<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
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

    protected function displayName(): Attribute
    {
        return Attribute::get(fn () => $this->name ? "{$this->code} - {$this->name}" : $this->code);
    }
}
