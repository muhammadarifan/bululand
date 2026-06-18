<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventDetail extends Model
{
    protected $fillable = [
        'event_id',
        'contribution_fee',
        'logo',
        'favicon',
        'hero_image',
        'hero_title',
        'hero_subtitle',
        'about_title',
        'about_content',
        'youtube_url',
        'contacts',
        'facebook_url',
        'instagram_url',
        'footer_text',
    ];

    protected function casts(): array
    {
        return [
            'contacts' => 'array',
        ];
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }
}
