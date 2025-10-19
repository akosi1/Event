<?php

namespace App\Services;

use App\Models\Event;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class EventRecurrenceService
{
    /**
     * Create recurring events based on the parent event
     */
    public function createRecurringEvents(Event $parentEvent, array $eventData): void
    {
        if (!$parentEvent->is_recurring || !$parentEvent->recurrence_pattern) {
            return;
        }

        $recurringEvents = $this->generateRecurringDates($parentEvent);
        
        foreach ($recurringEvents as $date) {
            $childEventData = array_merge($eventData, [
                'date' => $date,
                'parent_event_id' => $parentEvent->id,
                'is_recurring' => false, // Child events are not recurring themselves
                'recurrence_pattern' => null,
                'recurrence_interval' => null,
                'recurrence_end_date' => null,
                'recurrence_count' => null,
            ]);

            // Remove fields that shouldn't be copied to child events
            unset($childEventData['remove_image']);

            Event::create($childEventData);
        }
    }

    /**
     * Update an entire recurring series
     */
    public function updateRecurringSeries(Event $parentEvent, array $eventData): void
    {
        // Update all child events with the new data (except date and recurrence settings)
        $updateData = collect($eventData)
            ->except(['date', 'is_recurring', 'recurrence_pattern', 'recurrence_interval', 
                     'recurrence_end_date', 'recurrence_count', 'update_series', 'remove_image'])
            ->toArray();

        $parentEvent->childEvents()->update($updateData);
    }

    /**
     * Delete an entire recurring series
     */
    public function deleteRecurringSeries(Event $parentEvent): void
    {
        // Delete all child events first
        $parentEvent->childEvents()->delete();
        
        // Delete the parent event
        if ($parentEvent->image && \Storage::disk('public')->exists($parentEvent->image)) {
            \Storage::disk('public')->delete($parentEvent->image);
        }
        
        $parentEvent->delete();
    }

    /**
     * Generate recurring dates based on the event's recurrence pattern
     */
    private function generateRecurringDates(Event $event): Collection
    {
        $dates = collect();
        $currentDate = Carbon::parse($event->date);
        $interval = $event->recurrence_interval ?? 1;
        
        // Determine end condition
        $endDate = $event->recurrence_end_date ? 
            Carbon::parse($event->recurrence_end_date) : 
            $currentDate->copy()->addYear(); // Default to 1 year if no end date
            
        $maxCount = $event->recurrence_count ?? 52; // Default max 52 occurrences
        $count = 0;

        while ($currentDate->lte($endDate) && $count < $maxCount) {
            $nextDate = $this->calculateNextDate($currentDate, $event->recurrence_pattern, $interval);
            
            if ($nextDate->lte($endDate)) {
                $dates->push($nextDate->copy());
                $count++;
            }
            
            $currentDate = $nextDate;
        }

        return $dates;
    }

    /**
     * Calculate the next date based on recurrence pattern
     */
    private function calculateNextDate(Carbon $currentDate, string $pattern, int $interval): Carbon
    {
        switch ($pattern) {
            case 'daily':
                return $currentDate->copy()->addDays($interval);
                
            case 'weekly':
                return $currentDate->copy()->addWeeks($interval);
                
            case 'monthly':
                return $currentDate->copy()->addMonths($interval);
                
            case 'yearly':
                return $currentDate->copy()->addYears($interval);
                
            case 'weekdays':
                $nextDate = $currentDate->copy()->addDay();
                while ($nextDate->isWeekend()) {
                    $nextDate->addDay();
                }
                return $nextDate;
                
            default:
                return $currentDate->copy()->addDays($interval);
        }
    }

    /**
     * Get next occurrence of a recurring event
     */
    public function getNextOccurrence(Event $event): ?Carbon
    {
        if (!$event->is_recurring) {
            return null;
        }

        $nextChild = $event->childEvents()
            ->where('date', '>', now())
            ->where('status', 'active')
            ->orderBy('date', 'asc')
            ->first();

        return $nextChild ? Carbon::parse($nextChild->date) : null;
    }

    /**
     * Get all upcoming occurrences of a recurring event
     */
    public function getUpcomingOccurrences(Event $event, int $limit = 5): Collection
    {
        if (!$event->is_recurring) {
            return collect([$event]);
        }

        return $event->childEvents()
            ->where('date', '>', now())
            ->where('status', 'active')
            ->orderBy('date', 'asc')
            ->limit($limit)
            ->get();
    }
}