<?php

namespace App\Models;

use Illuminate\Database\Eloquent\{Factories\HasFactory, Model, Relations\HasMany, Relations\BelongsToMany, Relations\BelongsTo};
use Illuminate\Support\Facades\Storage;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'description', 'date', 'start_time', 'end_time', 'location', 'status', 
        'department', 'is_exclusive', 'allowed_departments', 'is_recurring', 
        'recurrence_pattern', 'recurrence_interval', 'recurrence_end_date', 
        'recurrence_count', 'repeat_type', 'repeat_interval', 'repeat_until',
        'parent_event_id', 'cancel_reason', 'image',
    ];

    protected $attributes = [
        'description' => null,
        'location' => null,
        'status' => 'active',
        'is_exclusive' => false,
        'is_recurring' => false,
        'department' => null,
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

    // Department constants
    const DEPARTMENTS = [
        'BSIT' => 'Bachelor of Science in Information Technology',
        'BSBA' => 'Bachelor of Science in Business Administration',
        'BSED' => 'Bachelor of Science in Education',
        'BEED' => 'Bachelor of Elementary Education',
        'BSHM' => 'Bachelor of Science in Hospitality Management'
    ];

    // Recurrence pattern constants
    const RECURRENCE_PATTERNS = [
        'daily' => 'Daily',
        'weekly' => 'Weekly',
        'monthly' => 'Monthly',
        'yearly' => 'Yearly',
        'weekdays' => 'Weekdays Only',
        'custom' => 'Custom'
    ];

    /**
     * Check if event has an image
     */
    public function hasImage(): bool
    {
        if (empty($this->image)) {
            return false;
        }

        // Check if it's a full URL
        if (filter_var($this->image, FILTER_VALIDATE_URL)) {
            return true;
        }

        // Check if file exists in storage
        try {
            return Storage::disk('public')->exists($this->image);
        } catch (\Exception $e) {
            \Log::warning('Error checking image existence: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get the image URL - Main accessor for all image URL needs
     */
    public function getImageUrlAttribute(): string
    {
        if (empty($this->image)) {
            return $this->getDefaultImage();
        }

        // If image is already a full URL, validate and return
        if (filter_var($this->image, FILTER_VALIDATE_URL)) {
            return $this->image;
        }

        // For relative paths, construct proper URL
        try {
            if (Storage::disk('public')->exists($this->image)) {
                // Use URL helper to generate proper signed/permanent URL
                $url = Storage::disk('public')->url($this->image);
                return url($url);
            }
        } catch (\Exception $e) {
            \Log::warning('Error retrieving image URL: ' . $e->getMessage());
        }

        return $this->getDefaultImage();
    }

    /**
     * Get default image path
     */
    private function getDefaultImage(): string
    {
        $defaultPath = 'images/default-event.jpg';
        
        // Check if default exists, otherwise use placeholder
        if (Storage::disk('public')->exists($defaultPath)) {
            return url(Storage::disk('public')->url($defaultPath));
        }

        // Fallback to public path
        return asset('images/placeholder-event.png');
    }

    /**
     * Get image path for display with fallback
     */
    public function getImagePath(): string
    {
        return $this->getImageUrlAttribute();
    }

    /**
     * Delete the event image from storage
     */
    public function deleteImage(): bool
    {
        if (empty($this->image)) {
            return true;
        }

        // Don't delete if it's a full URL (external image)
        if (filter_var($this->image, FILTER_VALIDATE_URL)) {
            return true;
        }

        try {
            if (Storage::disk('public')->exists($this->image)) {
                return Storage::disk('public')->delete($this->image);
            }
        } catch (\Exception $e) {
            \Log::error('Failed to delete event image: ' . $e->getMessage());
        }

        return true; // Return true to not block deletion
    }

    /**
     * Store image and return the path
     */
    public function storeImage($file, $folder = 'events'): ?string
    {
        try {
            if ($file === null) {
                return null;
            }

            // Delete old image if exists
            if ($this->image) {
                $this->deleteImage();
            }

            // Generate unique filename with timestamp
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            
            // Store file with proper visibility
            $path = $file->storeAs(
                $folder,
                $filename,
                'public'
            );

            if ($path === false) {
                \Log::error('Failed to store image file');
                return null;
            }

            return $path;
        } catch (\Exception $e) {
            \Log::error('Image storage error: ' . $e->getMessage());
            return null;
        }
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

    /**
     * Check if event is available for a specific department
     */
    public function isAvailableForUserDepartment($userDepartment): bool
    {
        if (!$this->is_exclusive) {
            return true;
        }

        return $this->isAvailableForDepartment($userDepartment);
    }

    /**
     * Original method kept for backward compatibility
     */
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

    /**
     * Get departments that can access this event
     */
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

    /**
     * Get full department names for display
     */
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

    /**
     * Check if a user can join this event
     */
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

    /**
     * Get events available for a specific user
     */
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

    /**
     * Boot method to handle model events
     */
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($event) {
            $event->deleteImage();
        });
    }
}