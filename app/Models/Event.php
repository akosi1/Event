<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Log;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'description', 'date', 'start_time', 'end_time', 'location', 'status',
        'department', 'is_exclusive', 'allowed_departments', 'is_recurring',
        'recurrence_pattern', 'recurrence_interval', 'recurrence_end_date',
        'recurrence_count', 'repeat_type', 'repeat_interval', 'repeat_until',
        'parent_event_id', 'cancel_reason', 'image', 'certificate_template_image'
    ];

    protected $casts = [
        'date' => 'datetime',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'repeat_until' => 'datetime',
        'recurrence_end_date' => 'datetime',
        'is_exclusive' => 'boolean',
        'is_recurring' => 'boolean',
        'allowed_departments' => 'array',
    ];

    const DEPARTMENTS = [
        'BSIT' => 'Bachelor of Science in Information Technology',
        'BSBA' => 'Bachelor of Science in Business Administration',
        'BSED' => 'Bachelor of Science in Education',
        'BEED' => 'Bachelor of Elementary Education',
        'BSHM' => 'Bachelor of Science in Hospitality Management'
    ];

    const RECURRENCE_PATTERNS = [
        'daily' => 'Daily',
        'weekly' => 'Weekly',
        'monthly' => 'Monthly',
        'yearly' => 'Yearly',
        'weekdays' => 'Weekdays Only',
        'custom' => 'Custom'
    ];

    // Relationships
    public function certificates(): HasMany
    {
        return $this->hasMany(Certificate::class);
    }

    public function joins(): HasMany
    {
        return $this->hasMany(EventJoin::class);
    }

    public function joinedUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'event_joins')
                    ->withTimestamps()
                    ->withPivot('joined_at');
    }

    public function parentEvent(): BelongsTo
    {
        return $this->belongsTo(Event::class, 'parent_event_id');
    }

    public function childEvents(): HasMany
    {
        return $this->hasMany(Event::class, 'parent_event_id');
    }

    // Image helpers
    public function hasImage(): bool
    {
        return !empty($this->image) && file_exists(storage_path('app/public/' . $this->image));
    }

    public function getImageUrlAttribute(): string
    {
        if (!$this->image) {
            return asset('images/default-event.jpg');
        }

        if (filter_var($this->image, FILTER_VALIDATE_URL)) {
            return $this->image;
        }

        $extension = strtolower(pathinfo($this->image, PATHINFO_EXTENSION));
        if (!in_array($extension, ['jpg', 'jpeg', 'png'])) {
            return asset('images/default-event.jpg');
        }

        return $this->hasImage()
            ? asset('storage/' . $this->image)
            : asset('images/default-event.jpg');
    }

    public function getImagePath(): string
    {
        return $this->hasImage()
            ? asset('storage/' . $this->image)
            : asset('images/default-event.jpg');
    }

    public function deleteImage(): bool
    {
        $path = storage_path('app/public/' . $this->image);
        if ($this->image && file_exists($path)) {
            try {
                unlink($path);
                return true;
            } catch (\Throwable $e) {
                Log::error('Failed to delete event image: ' . $e->getMessage());
                return false;
            }
        }
        return true;
    }

    // Join status & helpers
    public function joinStatus($userId): string
    {
        $join = $this->joins()->where('user_id', $userId)->first();

        if (!$join) return 'not_joined';
        if ($join->approved) return 'joined';
        return 'pending';
    }

    public function isJoinedByUser($userId): bool
    {
        return $this->joins()->where('user_id', $userId)->exists();
    }

    public function getJoinedCountAttribute(): int
    {
        return $this->joins()->count();
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeUpcoming($query)
    {
        return $query->where('date', '>=', now());
    }

    public function scopeForDepartment($query, $department)
    {
        return $query->where(function ($q) use ($department) {
            $q->where('is_exclusive', false)
              ->orWhere('department', $department)
              ->orWhereJsonContains('allowed_departments', $department);
        });
    }

    // Department logic
    public function isAvailableForUserDepartment($userDepartment): bool
    {
        if (!$this->is_exclusive) {
            return true;
        }

        return $this->isAvailableForDepartment($userDepartment);
    }

    public function isAvailableForDepartment($department): bool
    {
        if (!$this->is_exclusive) {
            return true;
        }

        if ($this->department === $department) {
            return true;
        }

        return $this->allowed_departments && in_array($department, $this->allowed_departments);
    }

    public function getAccessibleDepartments(): array
    {
        if (!$this->is_exclusive) {
            return array_keys(self::DEPARTMENTS);
        }

        $departments = [];

        if ($this->department) {
            $departments[] = $this->department;
        }

        if (is_array($this->allowed_departments)) {
            $departments = array_merge($departments, $this->allowed_departments);
        }

        return array_unique($departments);
    }

    public function getDepartmentDisplayAttribute(): string
    {
        return !$this->is_exclusive
            ? 'All Departments'
            : implode(', ', $this->getAccessibleDepartments());
    }

    public function getDepartmentNamesAttribute(): string
    {
        if (!$this->is_exclusive) {
            return 'Open to All Departments';
        }

        $departmentNames = [];
        foreach ($this->getAccessibleDepartments() as $deptCode) {
            $departmentNames[] = self::DEPARTMENTS[$deptCode] ?? $deptCode;
        }

        return implode(', ', $departmentNames);
    }

    // Joining rules
    public function canUserJoin($user): bool
    {
        return $this->status === 'active'
            && $this->date >= now()
            && !$this->isJoinedByUser($user->id)
            && $this->isAvailableForUserDepartment($user->department);
    }

    // Recurrence
    public function isRecurring(): bool
    {
        return $this->is_recurring && !empty($this->recurrence_pattern);
    }

    public function isChildEvent(): bool
    {
        return !is_null($this->parent_event_id);
    }

    public function getRecurrenceDisplayAttribute(): string
    {
        if (!$this->isRecurring()) {
            return 'One-time event';
        }

        $pattern = self::RECURRENCE_PATTERNS[$this->recurrence_pattern] ?? $this->recurrence_pattern;
        $interval = $this->recurrence_interval > 1 ? " (Every {$this->recurrence_interval})" : '';

        return $pattern . $interval;
    }

    // Query helper
    public static function availableForUser($user)
    {
        return static::where('status', 'active')
            ->where('date', '>=', now())
            ->where(function ($query) use ($user) {
                $query->where('is_exclusive', false)
                      ->orWhere('department', $user->department)
                      ->orWhereJsonContains('allowed_departments', $user->department);
            });
    }

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($event) {
            $event->deleteImage();
        });
    }
}
