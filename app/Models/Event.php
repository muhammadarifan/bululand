<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Event extends Model
{
    protected $fillable = ['name', 'subdomain', 'is_active', 'active_until'];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'active_until' => 'datetime',
        ];
    }

    public function eventDetail(): HasOne
    {
        return $this->hasOne(EventDetail::class);
    }
}
