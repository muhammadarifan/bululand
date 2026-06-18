<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Validation\ValidationException;

class EventMoneyTransaction extends Model
{
    protected $fillable = [
        'event_id',
        'house_id',
        'donor_name',
        'description',
        'type',
        'category',
        'amount',
        'attachment',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function house(): BelongsTo
    {
        return $this->belongsTo(House::class);
    }

    protected static function boot(): void
    {
        parent::boot();

        static::saving(function (self $transaction) {
            // Rule 1: If category is 'contribution', house_id must be filled
            if ($transaction->category === 'contribution' && blank($transaction->house_id)) {
                throw ValidationException::withMessages([
                    'house_id' => __('House is required when category is Contribution.'),
                ]);
            }

            // Rule 2: If house_id is null, donor_name must be filled
            if (blank($transaction->house_id) && blank($transaction->donor_name)) {
                throw ValidationException::withMessages([
                    'donor_name' => __('Donor Name is required when no House is selected.'),
                ]);
            }
        });
    }
}
