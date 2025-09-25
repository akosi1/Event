<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\{HasMany, BelongsTo, BelongsToMany};
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', // Add this for your controller
        'first_name', 
        'middle_name', 
        'last_name',
        'email', 
        'password', 
        'role', 
        'status', 
        'department',
        'student_id', // Add this for your controller
        'email_verified_at' // Add this for your controller
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get full name with middle name.
     */
    public function getFullNameAttribute(): string
    {
        // If using the 'name' field from controller, return that
        if (!empty($this->name)) {
            return $this->name;
        }
        
        // Otherwise, construct from individual name fields
        return trim($this->first_name . ' ' . $this->middle_name . ' ' . $this->last_name);
    }

    /**
     * Get full name with middle initial.
     */
    public function getFullNameWithInitialAttribute(): string
    {
        // If using the 'name' field, try to parse it
        if (!empty($this->name)) {
            return $this->name; // You could add logic to parse middle initial if needed
        }
        
        $middleInitial = $this->middle_name ? ' ' . substr($this->middle_name, 0, 1) . '.' : '';
        return trim($this->first_name . $middleInitial . ' ' . $this->last_name);
    }

    /**
     * Get the department name.
     */
    public function getDepartmentNameAttribute(): string
    {
        $departments = [
            'BSIT' => 'Bachelor of Science in Information Technology',
            'BSBA' => 'Bachelor of Science in Business Administration',
            'BSED' => 'Bachelor of Science in Education',
            'BEED' => 'Bachelor of Elementary Education',
            'BSHM' => 'Bachelor of Science in Hospitality Management'
        ];

        return $departments[$this->department] ?? $this->department;
    }

    /**
     * Check if user is admin.
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user is a student.
     */
    public function isStudent(): bool
    {
        return $this->role === 'student';
    }

    /**
     * Check if user account is active.
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Check if user belongs to a specific department.
     */
    public function belongsToDepartment(string $department): bool
    {
        return $this->department === $department;
    }

    /**
     * Get the student record associated with this user.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id', 'student_id');
    }

    /**
     * Alternative relationship by email (for your current controller logic)
     */
    public function studentByEmail(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'email', 'email');
    }

    /**
     * Check if user has MS365 email.
     */
    public function hasMs365Email(): bool
    {
        return str_ends_with($this->email, '@mcclawis.edu.ph');
    }

    /**
     * Scope for active users.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope for MS365 users.
     */
    public function scopeMs365($query)
    {
        return $query->where('email', 'like', '%@mcclawis.edu.ph');
    }

    /**
     * Events the user joined (via pivot).
     */
    public function joinedEvents(): BelongsToMany
    {
        return $this->belongsToMany(Event::class, 'event_joins')
                    ->withTimestamps()
                    ->withPivot('joined_at');
    }

    /**
     * Event joins relationship (direct).
     */
    public function eventJoins(): HasMany
    {
        return $this->hasMany(EventJoin::class);
    }
}