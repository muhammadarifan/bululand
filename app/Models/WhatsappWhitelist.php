<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WhatsappWhitelist extends Model
{
    protected $fillable = [
        'phone',
        'is_whitelisted',
    ];
}
