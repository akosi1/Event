<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrintSummary extends Model
{
    use HasFactory;

    protected $fillable = [
        'left_logo_path',
        'right_logo_path',
        'description',
        'events_left_logo_path',
        'events_right_logo_path',
        'events_description',
    ];

    /**
     * Get left logo URL for Event Joins
     */
    public function getLeftLogoUrlAttribute(): string
    {
        return $this->left_logo_path 
            ? asset('storage/logos/' . $this->left_logo_path)
            : asset('images/default-left-logo.png');
    }

    /**
     * Get right logo URL for Event Joins
     */
    public function getRightLogoUrlAttribute(): string
    {
        return $this->right_logo_path 
            ? asset('storage/logos/' . $this->right_logo_path)
            : asset('images/default-right-logo.png');
    }

    /**
     * Get left logo URL for Events
     */
    public function getEventsLeftLogoUrlAttribute(): string
    {
        return $this->events_left_logo_path 
            ? asset('storage/logos/' . $this->events_left_logo_path)
            : asset('images/default-left-logo.png');
    }

    /**
     * Get right logo URL for Events
     */
    public function getEventsRightLogoUrlAttribute(): string
    {
        return $this->events_right_logo_path 
            ? asset('storage/logos/' . $this->events_right_logo_path)
            : asset('images/default-right-logo.png');
    }

    /**
     * Generate summary data for event joins
     */
    public static function generateEventJoinsSummary($eventJoins, $settings = null)
    {
        $settings = $settings ?? self::first();
        
        return [
            'left_logo' => $settings?->left_logo_url ?? asset('images/default-left-logo.png'),
            'right_logo' => $settings?->right_logo_url ?? asset('images/default-right-logo.png'),
            'description' => $settings?->description ?? 'Event Join Requests Summary Report',
            'generated_at' => now()->format('F d, Y h:i A'),
            'total_records' => $eventJoins->count(),
            'approved_count' => $eventJoins->where('approved', true)->count(),
            'pending_count' => $eventJoins->where('approved', false)->count(),
            'event_joins' => $eventJoins
        ];
    }

    /**
     * Generate summary data for events
     */
    public static function generateEventsSummary($events, $settings = null)
    {
        $settings = $settings ?? self::first();
        
        // Calculate statistics
        $totalEvents = $events->count();
        $activeCount = $events->where('status', 'active')->count();
        $postponedCount = $events->where('status', 'postponed')->count();
        $cancelledCount = $events->where('status', 'cancelled')->count();
        $recurringCount = $events->where('is_recurring', true)->count();
        $exclusiveCount = $events->where('is_exclusive', true)->count();
        
        // Department breakdown
        $departmentStats = [];
        foreach ($events as $event) {
            if ($event->is_exclusive) {
                if ($event->department) {
                    $dept = $event->department;
                    if (!isset($departmentStats[$dept])) {
                        $departmentStats[$dept] = 0;
                    }
                    $departmentStats[$dept]++;
                }
                if ($event->allowed_departments) {
                    foreach ($event->allowed_departments as $dept) {
                        if (!isset($departmentStats[$dept])) {
                            $departmentStats[$dept] = 0;
                        }
                        $departmentStats[$dept]++;
                    }
                }
            }
        }
        
        return [
            'left_logo' => $settings?->events_left_logo_url ?? asset('images/default-left-logo.png'),
            'right_logo' => $settings?->events_right_logo_url ?? asset('images/default-right-logo.png'),
            'description' => $settings?->events_description ?? 'Events Management Summary Report',
            'generated_at' => now()->format('F d, Y h:i A'),
            'total_events' => $totalEvents,
            'active_count' => $activeCount,
            'postponed_count' => $postponedCount,
            'cancelled_count' => $cancelledCount,
            'recurring_count' => $recurringCount,
            'exclusive_count' => $exclusiveCount,
            'open_count' => $totalEvents - $exclusiveCount,
            'department_stats' => $departmentStats,
            'events' => $events
        ];
    }
}