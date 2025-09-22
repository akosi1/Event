<?php

namespace App\Models;

use Illuminate\Database\Eloquent\{Model, Factories\HasFactory};

class Notification extends Model
{
    use HasFactory;

    protected $fillable = ['type', 'message', 'data', 'is_read'];
    protected $casts = ['data' => 'array', 'is_read' => 'boolean'];

    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    public function scopeRecent($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    public function markAsRead()
    {
        $this->update(['is_read' => true]);
    }
}