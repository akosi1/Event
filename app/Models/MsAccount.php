<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MsAccount extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'ms_accounts';

    protected $fillable = [
        'firstname',
        'lastname',
        'username',
    ];

    protected $dates = [
        'deleted_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get the full name attribute
     */
    public function getFullNameAttribute()
    {
        return $this->firstname . ' ' . $this->lastname;
    }

    /**
     * Scope to search accounts
     */
    public function scopeSearch($query, $term)
    {
        return $query->where(function($q) use ($term) {
            $q->where('firstname', 'like', '%' . $term . '%')
              ->orWhere('lastname', 'like', '%' . $term . '%')
              ->orWhere('username', 'like', '%' . $term . '%');
        });
    }
}