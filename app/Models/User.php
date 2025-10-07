<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'id_number',
        'first_name',
        'middle_name',
        'last_name',
        'email',
        'password',
        'role',
        'status',
        'department',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    // Encrypt id_number before saving
    public function setIdNumberAttribute($value)
    {
        $this->attributes['id_number'] = encrypt($value);
    }

    // Decrypt id_number when retrieving
    public function getIdNumberAttribute($value)
    {
        return $value ? decrypt($value) : null;
    }

    public function getFullNameAttribute(): string
    {
        return trim($this->first_name . ' ' . $this->middle_name . ' ' . $this->last_name);
    }

    public function getFullNameWithInitialAttribute(): string
    {
        $middleInitial = $this->middle_name ? ' ' . substr($this->middle_name, 0, 1) . '.' : '';
        return trim($this->first_name . $middleInitial . ' ' . $this->last_name);
    }

    public function getDepartmentNameAttribute(): string
    {
        $departments = [
            'BSIT' => 'Bachelor of Science in Information Technology',
            'BSBA' => 'Bachelor of Science in Business Administration',
            'BSED' => 'Bachelor of Science in Education',
            'BEED' => 'Bachelor of Elementary Education',
            'BSHM' => 'Bachelor of Science in Hospitality Management',
        ];

        return $departments[$this->department] ?? $this->department;
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function belongsToDepartment(string $department): bool
    {
        return $this->department === $department;
    }

    public function eventJoins(): HasMany
    {
        return $this->hasMany(EventJoin::class);
    }

    public function joinedEvents(): BelongsToMany
    {
        return $this->belongsToMany(Event::class, 'event_joins')
                    ->withTimestamps()
                    ->withPivot('joined_at');
    }
}
