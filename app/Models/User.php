<?php

namespace App\Models;
use App\Notifications\ResetPasswordNotification;
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
        'year_level',
        'profile_picture',
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
        if (!$value) {
            return null;
        }

        try {
            return decrypt($value);
        } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
            // If decryption fails, the value is likely not encrypted
            // Return the plain value as-is
            return $value;
        }
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

    public function getDepartmentNameAttribute(): ?string
    {
        if (!$this->department) {
            return null;
        }

        $departments = [
            'BSIT' => 'Bachelor of Science in Information Technology',
            'BSBA' => 'Bachelor of Science in Business Administration',
            'BSED' => 'Bachelor of Science in Education',
            'BEED' => 'Bachelor of Elementary Education',
            'BSHM' => 'Bachelor of Science in Hospitality Management',
        ];

        return $departments[$this->department] ?? $this->department;
    }

    public function getYearLevelNameAttribute(): ?string
    {
        if (!$this->year_level) {
            return null;
        }

        $years = [
            '1' => '1st Year',
            '2' => '2nd Year',
            '3' => '3rd Year',
            '4' => '4th Year',
        ];

        return $years[$this->year_level] ?? $this->year_level;
    }

    public function getProfilePictureUrlAttribute(): ?string
    {
        return $this->profile_picture;
    }

    public function getInitialsAttribute(): string
    {
        return substr($this->first_name, 0, 1) . substr($this->last_name, 0, 1);
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function certificates(): HasMany
    {
        return $this->hasMany(Certificate::class);
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

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }
    // Add this to your existing User model

public function loginLogs(): HasMany
{
    return $this->hasMany(LoginLog::class);
}

public function activeLoginSessions(): HasMany
{
    return $this->hasMany(LoginLog::class)->where('status', 'active');
}
}