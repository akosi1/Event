<?php

namespace App\Models;

use Illuminate\Database\Eloquent\{Model, Relations\BelongsTo};

class EventJoin extends Model
{
    protected $fillable = ['user_id', 'event_id', 'joined_at'];
    
    protected $casts = [
        'joined_at' => 'datetime'
    ];

    /**
     * Get the user that joined the event
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the event that was joined
     */
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * Boot the model to set joined_at automatically
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($eventJoin) {
            if (is_null($eventJoin->joined_at)) {
                $eventJoin->joined_at = now();
            }
        });
    }
}