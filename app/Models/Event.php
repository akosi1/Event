<?php

namespace App\Models;

use Illuminate\Database\Eloquent\{
    Factories\HasFactory,
    Model,
    Relations\HasMany,
    Relations\BelongsToMany,
    Relations\BelongsTo
};
use Illuminate\Support\Facades\Storage;

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

    public function certificates(): HasMany
    {
        return $this->hasMany(Certificate::class);
    }
    public function hasImage(): bool
    {
        return !empty($this->image) && file_exists(public_path('app/public/' . $this->image));
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

        if (file_exists(public_path('storage/' . $this->image))) {
            return asset('storage/' . $this->image);
        }

        return asset('images/default-event.jpg');
    }

    public function getImagePath(): string
    {
        if ($this->hasImage()) {
            return asset('app/public/' . $this->image);
        }

        return asset('images/default-event.jpg');
    }

    public function deleteImage(): bool
    {
        $path = public_path('app/public/' . $this->image);
        if ($this->image && file_exists($path)) {
            try {
                unlink($path);
                return true;
            } catch (\Exception $e) {
                \Log::error('Failed to delete event image: ' . $e->getMessage());
                return false;
            }
        }
        return true;
    }

    public function joins(): HasMany
    {
        return $this->hasMany(EventJoin::class);
    }

    public function joinStatus($userId)
    {
        $join = $this->joins()->where('user_id', $userId)->first();

        if (!$join) return 'not_joined';
        if ($join->approved) return 'joined';
        return 'pending';
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

    public function isJoinedByUser($userId): bool
    {
        return $this->joins()->where('user_id', $userId)->exists();
    }

    public function getJoinedCountAttribute(): int
    {
        return $this->joins()->count();
    }

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

        if ($this->allowed_departments && in_array($department, $this->allowed_departments)) {
            return true;
        }

        return false;
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

        if ($this->allowed_departments && is_array($this->allowed_departments)) {
            $departments = array_merge($departments, $this->allowed_departments);
        }

        return array_unique($departments);
    }

    public function getDepartmentDisplayAttribute(): string
    {
        if (!$this->is_exclusive) {
            return 'All Departments';
        }

        $departments = $this->getAccessibleDepartments();
        return implode(', ', $departments);
    }

    public function getDepartmentNamesAttribute(): string
    {
        if (!$this->is_exclusive) {
            return 'Open to All Departments';
        }

        $accessibleDepartments = $this->getAccessibleDepartments();
        $departmentNames = [];

        foreach ($accessibleDepartments as $deptCode) {
            $departmentNames[] = self::DEPARTMENTS[$deptCode] ?? $deptCode;
        }

        return implode(', ', $departmentNames);
    }

    public function canUserJoin($user): bool
    {
        if ($this->status !== 'active') {
            return false;
        }

        if ($this->date < now()) {
            return false;
        }

        if ($this->isJoinedByUser($user->id)) {
            return false;
        }

        return $this->isAvailableForUserDepartment($user->department);
    }

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
