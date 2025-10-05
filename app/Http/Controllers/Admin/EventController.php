<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Services\EventRecurrenceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class EventController extends Controller
{
    protected $eventRecurrenceService;

    public function __construct(EventRecurrenceService $eventRecurrenceService)
    {
        $this->eventRecurrenceService = $eventRecurrenceService;
    }

    // Define departments array to ensure consistency
    const DEPARTMENTS = [
        'BSIT' => 'Bachelor of Science in Information Technology',
        'BSBA' => 'Bachelor of Science in Business Administration',
        'BSED' => 'Bachelor of Science in Education',
        'BEED' => 'Bachelor of Elementary Education',
        'BSHM' => 'Bachelor of Science in Hospitality Management'
    ];

    public function index(Request $request)
    {
        $query = Event::with(['parentEvent', 'childEvents'])
                      ->whereNull('parent_event_id'); // Only show parent events

        // Search filter
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%')
                  ->orWhere('location', 'like', '%' . $request->search . '%');
            });
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Department filter
        if ($request->filled('department')) {
            $query->where(function ($q) use ($request) {
                $q->where('department', $request->department)
                  ->orWhereJsonContains('allowed_departments', $request->department);
            });
        }

        // Exclusivity filter
        if ($request->filled('exclusivity')) {
            if ($request->exclusivity === 'exclusive') {
                $query->where('is_exclusive', true);
            } elseif ($request->exclusivity === 'open') {
                $query->where('is_exclusive', false);
            }
        }

        // Recurrence filter
        if ($request->filled('recurrence')) {
            if ($request->recurrence === 'recurring') {
                $query->where('is_recurring', true);
            } elseif ($request->recurrence === 'one_time') {
                $query->where('is_recurring', false);
            }
        }

        $perPage = $request->get('per_page', 10);
        $events = $query->orderBy('date', 'desc')->paginate($perPage);

        // Append query parameters to pagination links
        $events->appends($request->query());

        return view('admin.events.index', compact('events'));
    }

    public function create()
    {
        return view('admin.events.create');
    }

    public function store(Request $request)
    {
        $validated = $this->validateEventData($request);

        // Handle image upload
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = Str::random(20) . '.' . $image->getClientOriginalExtension();
            $imagePath = $image->storeAs('events', $imageName, 'public');
            $validated['image'] = $imagePath;
        }

        // Process department exclusivity
        $validated = $this->processDepartmentExclusivity($validated, $request);

        // Create the main event
        $event = Event::create($validated);

        // Handle recurring events
        if ($request->boolean('is_recurring') && $request->filled('recurrence_pattern')) {
            $this->eventRecurrenceService->createRecurringEvents($event, $validated);
        }

        return redirect()->route('admin.events.index')
                        ->with('success', 'Event created successfully!' . 
                               ($event->is_recurring ? ' Recurring instances have been generated.' : ''));
    }

    public function show(Event $event)
    {
        // Load child events and joined users with their departments
        $event->load([
            'childEvents' => function ($query) {
                $query->orderBy('date', 'asc');
            },
            'joinedUsers' => function ($query) {
                $query->select('users.id', 'users.first_name', 'users.last_name', 'users.department', 'users.email')
                      ->withPivot('joined_at')
                      ->orderBy('event_joins.joined_at', 'desc');
            }
        ]);

        // Get department statistics for joined users
        $departmentStats = $event->joinedUsers->groupBy('department')->map(function ($users) {
            return [
                'count' => $users->count(),
                'users' => $users
            ];
        });
        
        return view('admin.events.show', compact('event', 'departmentStats'));
    }

    public function edit(Event $event)
    {
        return view('admin.events.edit', compact('event'));
    }

    public function update(Request $request, Event $event)
    {
        $validated = $this->validateEventData($request, $event);

        // Handle image removal
        if ($request->filled('remove_image') && $request->remove_image == '1') {
            if ($event->image && Storage::disk('public')->exists($event->image)) {
                Storage::disk('public')->delete($event->image);
            }
            $validated['image'] = null;
        }

        // Handle new image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($event->image && Storage::disk('public')->exists($event->image)) {
                Storage::disk('public')->delete($event->image);
            }

            $image = $request->file('image');
            $imageName = Str::random(20) . '.' . $image->getClientOriginalExtension();
            $imagePath = $image->storeAs('events', $imageName, 'public');
            $validated['image'] = $imagePath;
        }

        // Process department exclusivity
        $validated = $this->processDepartmentExclusivity($validated, $request);

        // Remove the remove_image flag from validated data before updating
        unset($validated['remove_image']);

        $event->update($validated);

        // Handle recurring event updates
        if ($request->boolean('update_series') && $event->isRecurring()) {
            $this->eventRecurrenceService->updateRecurringSeries($event, $validated);
            $message = 'Event series updated successfully!';
        } else {
            $message = 'Event updated successfully!';
        }

        return redirect()->route('admin.events.index')
                        ->with('success', $message);
    }

    public function destroy(Event $event)
    {
        // Handle recurring event deletion
        if ($event->isRecurring() && request()->boolean('delete_series')) {
            $this->eventRecurrenceService->deleteRecurringSeries($event);
            $message = 'Event series deleted successfully!';
        } else {
            // Delete associated image
            if ($event->image && Storage::disk('public')->exists($event->image)) {
                Storage::disk('public')->delete($event->image);
            }
            $event->delete();
            $message = 'Event deleted successfully!';
        }

        return redirect()->route('admin.events.index')
                        ->with('success', $message);
    }

    /**
     * Generate print summary for events
     */
    public function printSummary(Request $request)
    {
        $query = Event::with(['childEvents', 'joinedUsers']);

        // Apply same filters as index
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%')
                  ->orWhere('location', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('department')) {
            $query->where(function ($q) use ($request) {
                $q->where('department', $request->department)
                  ->orWhereJsonContains('allowed_departments', $request->department);
            });
        }

        $events = $query->orderBy('date', 'desc')->get();
        
        // Calculate statistics
        $stats = [
            'total' => $events->count(),
            'active' => $events->where('status', 'active')->count(),
            'postponed' => $events->where('status', 'postponed')->count(),
            'cancelled' => $events->where('status', 'cancelled')->count(),
            'exclusive' => $events->where('is_exclusive', true)->count(),
            'open' => $events->where('is_exclusive', false)->count(),
            'recurring' => $events->where('is_recurring', true)->count(),
            'one_time' => $events->where('is_recurring', false)->count(),
            'by_department' => $events->where('is_exclusive', true)
                                    ->whereNotNull('department')
                                    ->groupBy('department')
                                    ->map->count(),
            'upcoming' => $events->where('date', '>=', now())->count(),
            'past' => $events->where('date', '<', now())->count(),
            'total_participants' => $events->sum(function($event) {
                return $event->joinedUsers->count();
            }),
            'participants_by_department' => $events->flatMap(function($event) {
                return $event->joinedUsers;
            })->groupBy('department')->map->count(),
        ];

        return view('admin.events.print-summary', compact('events', 'stats', 'request'));
    }

    /**
     * Get available departments
     */
    public static function getDepartments()
    {
        return self::DEPARTMENTS;
    }

    /**
     * Validate event data
     */
    private function validateEventData(Request $request, Event $event = null)
    {
        $rules = [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'date' => 'required|date',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i|after:start_time',
            'location' => 'required|string|max:255',
            'department' => 'nullable|string|in:' . implode(',', array_keys(self::DEPARTMENTS)),
            'status' => 'required|in:active,postponed,cancelled',
            'cancel_reason' => 'required_if:status,postponed,cancelled|nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'remove_image' => 'nullable|boolean',
            
            // Exclusivity fields
            'is_exclusive' => 'boolean',
            'allowed_departments' => 'nullable|array',
            'allowed_departments.*' => 'string|in:' . implode(',', array_keys(self::DEPARTMENTS)),
            
            // Recurrence fields
            'is_recurring' => 'boolean',
            'recurrence_pattern' => 'nullable|string|in:daily,weekly,monthly,yearly,weekdays,custom',
            'recurrence_interval' => 'nullable|integer|min:1|max:365',
            'recurrence_end_date' => 'nullable|date|after:date',
            'recurrence_count' => 'nullable|integer|min:1|max:365',
            
            // Update options
            'update_series' => 'boolean',
        ];

        // For new events, date should be in the future
        if (!$event) {
            $rules['date'] = 'required|date|after:now';
        }

        // Custom validation for exclusive events
        $validated = $request->validate($rules);

        // Additional validation for exclusive events
        if ($request->boolean('is_exclusive')) {
            if (empty($validated['department']) && empty($validated['allowed_departments'])) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'department' => 'For exclusive events, you must specify at least one department or select allowed departments.'
                ]);
            }
        }

        return $validated;
    }

    /**
     * Process department exclusivity settings
     */
    private function processDepartmentExclusivity(array $validated, Request $request): array
    {
        if ($request->boolean('is_exclusive')) {
            $validated['is_exclusive'] = true;
            
            // Ensure at least one department is specified for exclusive events
            if (empty($validated['department']) && empty($validated['allowed_departments'])) {
                $validated['department'] = array_keys(self::DEPARTMENTS)[0]; // Default to first department
            }
        } else {
            $validated['is_exclusive'] = false;
            $validated['department'] = null;
            $validated['allowed_departments'] = null;
        }

        return $validated;
    }
}