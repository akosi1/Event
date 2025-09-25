<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'first_name',
        'middle_name', 
        'last_name',
        'email',
        'department',
        'year_level',
        'status',
        'email_verified_at'
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'year_level' => 'integer',
    ];

    /**
     * Get the user account associated with this student.
     */
    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'email', 'email');
    }

    /**
     * Get full name with middle name.
     */
    public function getFullNameAttribute(): string
    {
        return trim($this->first_name . ' ' . $this->middle_name . ' ' . $this->last_name);
    }

    /**
     * Get full name with middle initial.
     */
    public function getFullNameWithInitialAttribute(): string
    {
        $middleInitial = $this->middle_name ? ' ' . substr($this->middle_name, 0, 1) . '.' : '';
        return trim($this->first_name . $middleInitial . ' ' . $this->last_name);
    }

    /**
     * Get the department full name.
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
     * Get the year level with suffix.
     */
    public function getYearLevelNameAttribute(): string
    {
        $suffixes = [1 => '1st', 2 => '2nd', 3 => '3rd', 4 => '4th'];
        return ($suffixes[$this->year_level] ?? $this->year_level) . ' Year';
    }

    /**
     * Check if student account is active.
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Check if student belongs to a specific department.
     */
    public function belongsToDepartment(string $department): bool
    {
        return $this->department === $department;
    }

    /**
     * Check if email is MS365 format.
     */
    public static function isMs365Email(string $email): bool
    {
        return str_ends_with($email, '@mcclawis.edu.ph');
    }

    /**
     * Generate a unique student ID.
     */
    public static function generateStudentId(string $department, int $yearLevel): string
    {
        $currentYear = date('Y');
        $departmentCode = strtoupper($department);
        
        // Get the last student ID for this department and year
        $lastStudent = self::where('department', $department)
            ->where('student_id', 'like', $currentYear . $departmentCode . '%')
            ->orderBy('student_id', 'desc')
            ->first();

        if ($lastStudent) {
            // Extract the sequence number and increment
            $lastSequence = (int) substr($lastStudent->student_id, -4);
            $newSequence = $lastSequence + 1;
        } else {
            $newSequence = 1;
        }

        return $currentYear . $departmentCode . str_pad($newSequence, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($student) {
            if (empty($student->student_id)) {
                $student->student_id = self::generateStudentId($student->department, $student->year_level);
            }
            
            if (empty($student->status)) {
                $student->status = 'active';
            }
        });
    }

    /**
     * Scope for active students.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope for specific department.
     */
    public function scopeDepartment($query, $department)
    {
        return $query->where('department', $department);
    }

    /**
     * Scope for specific year level.
     */
    public function scopeYearLevel($query, $yearLevel)
    {
        return $query->where('year_level', $yearLevel);
    }

    /**
     * Scope for MS365 emails.
     */
    public function scopeMs365($query)
    {
        return $query->where('email', 'like', '%@mcclawis.edu.ph');
    }
}