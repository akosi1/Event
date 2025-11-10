<?php

namespace App\Models;

use Illuminate\Database\Eloquent\{Model, Relations\BelongsTo};
use Illuminate\Support\Str;

class EventJoin extends Model
{
    protected $fillable = [
        'user_id', 
        'event_id', 
        'token',
        'joined_at', 
        'approved', 
        'approved_by', 
        'approved_at'
    ];

    protected $casts = [
        'joined_at' => 'datetime',
        'approved_at' => 'datetime'
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
     * Get the admin who approved the join
     */
    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function approve(User $admin)
    {
        $this->approved = true;
        $this->approved_by = $admin->id;
        $this->approved_at = now();
        $this->save();
    }

    public function reject(User $admin)
    {
        $this->approved = false;
        $this->approved_by = $admin->id;
        $this->approved_at = now();
        $this->save();
    }

    public function scopeApproved($query)
    {
        return $query->where('approved', true);
    }

    public function scopePending($query)
    {
        return $query->where('approved', false);
    }

    /**
     * Generate a unique token for this event join
     */
    public function generateToken(): string
    {
        do {
            $token = Str::random(32);
        } while (static::where('token', $token)->exists());

        $this->token = $token;
        $this->save();

        return $token;
    }

    /**
     * Boot the model to set joined_at and token automatically
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($eventJoin) {
            if (is_null($eventJoin->joined_at)) {
                $eventJoin->joined_at = now();
            }
            
            if (is_null($eventJoin->token)) {
                do {
                    $token = Str::random(32);
                } while (static::where('token', $token)->exists());
                
                $eventJoin->token = $token;
            }
        });
    }
}