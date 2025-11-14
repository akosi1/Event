<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Services\EventRecurrenceService;
use App\Services\EventNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use App\Models\Feedback;
use App\Mail\PHPMailerService;
use Illuminate\Support\Facades\Auth;

class EventController extends Controller
{
    protected $eventRecurrenceService;
    protected $eventNotificationService;

    public function __construct(
        EventRecurrenceService $eventRecurrenceService,
        EventNotificationService $eventNotificationService
    ) {
        $this->eventRecurrenceService = $eventRecurrenceService;
        $this->eventNotificationService = $eventNotificationService;
    }

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
                      ->whereNull('parent_event_id');

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%')
                  ->orWhere('location', 'like', '%' . $request->search . '%')
                  ->orWhere('token', 'like', '%' . $request->search . '%');
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

        if ($request->filled('exclusivity')) {
            if ($request->exclusivity === 'exclusive') {
                $query->where('is_exclusive', true);
            } elseif ($request->exclusivity === 'open') {
                $query->where('is_exclusive', false);
            }
        }

        if ($request->filled('recurrence')) {
            if ($request->recurrence === 'recurring') {
                $query->where('is_recurring', true);
            } elseif ($request->recurrence === 'one_time') {
                $query->where('is_recurring', false);
            }
        }

        $perPage = $request->get('per_page', 10);
        $events = $query->orderBy('date', 'desc')->paginate($perPage);

        $events->appends($request->query());

        return view('admin.events.index', compact('events'));
    }

    public function print(Request $request)
    {
        $query = Event::with(['parentEvent', 'childEvents', 'joinedUsers'])
                      ->whereNull('parent_event_id');

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%')
                  ->orWhere('location', 'like', '%' . $request->search . '%')
                  ->orWhere('token', 'like', '%' . $request->search . '%');
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

        if ($request->filled('exclusivity')) {
            if ($request->exclusivity === 'exclusive') {
                $query->where('is_exclusive', true);
            } elseif ($request->exclusivity === 'open') {
                $query->where('is_exclusive', false);
            }
        }

        if ($request->filled('recurrence')) {
            if ($request->recurrence === 'recurring') {
                $query->where('is_recurring', true);
            } elseif ($request->recurrence === 'one_time') {
                $query->where('is_recurring', false);
            }
        }

        // Get all events for printing (no pagination)
        $events = $query->orderBy('date', 'asc')->get();
        
        // Generate summary data
        $summaryData = [
            'events' => $events,
            'total_events' => $events->count(),
            'active_count' => $events->where('status', 'active')->count(),
            'postponed_count' => $events->where('status', 'postponed')->count(),
            'cancelled_count' => $events->where('status', 'cancelled')->count(),
            'generated_at' => now()->format('F d, Y h:i A')
        ];

        return view('admin.events.print', compact('summaryData'));
    }

    public function create()
    {
        return view('admin.events.create');
    }

    public function store(Request $request)
    {
        $validated = $this->validateEventData($request);

        $fileFields = ['image', 'certificate_template_image'];

        foreach ($fileFields as $field) {
            if ($request->filled($field)) {
                $base64String = $request->input($field);

                if (!preg_match('/^data:image\/(jpeg|png|jpg);base64,/', $base64String)) {
                    return back()->withErrors([$field => 'Invalid base64 image format.'])->withInput();
                }

                $validated[$field] = $base64String;
            }
        }

        $validated = $this->processDepartmentExclusivity($validated, $request);

        // Token will be auto-generated by the model's boot method
        $event = Event::create($validated);

        if ($request->boolean('is_recurring') && $request->filled('recurrence_pattern')) {
            $this->eventRecurrenceService->createRecurringEvents($event, $validated);
        }

        $notificationMessage = '';
        try {
            $sentCount = $this->eventNotificationService->notifyNewEvent($event);
            $notificationMessage = " Email notifications sent to {$sentCount} user(s).";
        } catch (\Exception $e) {
            \Log::error('Failed to send event notifications: ' . $e->getMessage());
            $notificationMessage = ' (Note: Some email notifications failed to send)';
        }

        return redirect()->route('admin.events.index')
                        ->with('success', 'Event "' . $event->title . '" created successfully! (Token: ' . $event->token . ')' .
                               ($event->is_recurring ? ' Recurring instances have been generated.' : '') .
                               $notificationMessage);
    }

    public function show(Event $event)
    {
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

        if ($request->filled('remove_image') && $request->remove_image == '1') {
            if ($event->image && Storage::disk('public')->exists($event->image)) {
                try {
                    Storage::disk('public')->delete($event->image);
                } catch (\Exception $e) {
                    \Log::error('Failed to delete old image: ' . $e->getMessage());
                }
            }
            $validated['image'] = null;
        }

        $fileFields = ['image', 'certificate_template_image'];

        foreach($fileFields as $field){
            if ($request->hasFile($field)) {
                try {
                    $image = $request->file($field);

                    if (!$image->isValid()) {
                        return back()->withErrors([$field => 'Invalid image file.'])->withInput();
                    }

                    if ($event->image && Storage::disk('public')->exists($event->image)) {
                        try {
                            Storage::disk('public')->delete($event->image);
                        } catch (\Exception $e) {
                            \Log::error('Failed to delete old image: ' . $e->getMessage());
                        }
                    }

                    $imageName = time() . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension();

                    $folder = $field === 'certificate_template_image' ? 'certificates' : 'events';
                    $imagePath = $image->storeAs($folder, $imageName, 'public');

                    if (!Storage::disk('public')->exists($imagePath)) {
                        return back()->withErrors(['image' => 'Failed to store image.'])->withInput();
                    }

                    $validated[$field] = $imagePath;
                } catch (\Exception $e) {
                    \Log::error('Image upload failed: ' . $e->getMessage());
                    return back()->withErrors(['image' => 'Failed to upload image: ' . $e->getMessage()])->withInput();
                }
            }
        }

        $validated = $this->processDepartmentExclusivity($validated, $request);

        unset($validated['remove_image']);

        $oldStatus = $event->status;

        $event->update($validated);

        if ($request->boolean('update_series') && $event->isRecurring()) {
            $this->eventRecurrenceService->updateRecurringSeries($event, $validated);
            $message = 'Event series "' . $event->title . '" updated successfully!';
        } else {
            $message = 'Event "' . $event->title . '" updated successfully!';
        }

        if ($event->status === 'active' && $oldStatus === 'active') {
            try {
                $sentCount = $this->eventNotificationService->notifyEventUpdate($event);
                if ($sentCount > 0) {
                    $message .= " Update notifications sent to {$sentCount} joined user(s).";
                }
            } catch (\Exception $e) {
                \Log::error('Failed to send update notifications: ' . $e->getMessage());
            }
        } elseif (in_array($event->status, ['cancelled', 'postponed']) && $oldStatus === 'active') {
            try {
                $sentCount = $this->eventNotificationService->notifyEventCancellation($event);
                if ($sentCount > 0) {
                    $message .= " Cancellation notifications sent to {$sentCount} joined user(s).";
                }
            } catch (\Exception $e) {
                \Log::error('Failed to send cancellation notifications: ' . $e->getMessage());
            }
        }

        return redirect()->route('admin.events.index')
                        ->with('success', $message);
    }

    public function destroy(Event $event)
    {
        if ($event->isRecurring() && request()->boolean('delete_series')) {
            $this->eventRecurrenceService->deleteRecurringSeries($event);
            $message = 'Event series "' . $event->title . '" deleted successfully!';
        } else {
            if ($event->image && Storage::disk('public')->exists($event->image)) {
                try {
                    Storage::disk('public')->delete($event->image);
                } catch (\Exception $e) {
                    \Log::error('Failed to delete image: ' . $e->getMessage());
                }
            }
            $event->delete();
            $message = 'Event "' . $event->title . '" deleted successfully!';
        }

        return redirect()->route('admin.events.index')
                        ->with('success', $message);
    }

    /**
     * Find event by token (for public access or API)
     */
    public function showByToken(string $token)
    {
        $event = Event::findByToken($token);

        if (!$event) {
            abort(404, 'Event not found');
        }

        return $this->show($event);
    }

    /**
     * Regenerate token for an event
     */
    public function regenerateToken(Event $event)
    {
        $oldToken = $event->token;
        $event->token = Event::generateUniqueToken();
        $event->save();

        return redirect()->back()->with('success', 'Event token regenerated successfully! New token: ' . $event->token);
    }

    public static function getDepartments()
    {
        return self::DEPARTMENTS;
    }

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
            'image' => ['nullable', 'string', 'regex:/^data:image\/(jpeg|png|jpg);base64,/'],
            'remove_image' => 'nullable|boolean',
            'is_exclusive' => 'boolean',
            'allowed_departments' => 'nullable|array',
            'allowed_departments.*' => 'string|in:' . implode(',', array_keys(self::DEPARTMENTS)),
            'is_recurring' => 'boolean',
            'recurrence_pattern' => 'nullable|string|in:daily,weekly,monthly,yearly,weekdays,custom',
            'recurrence_interval' => 'nullable|integer|min:1|max:365',
            'recurrence_end_date' => 'nullable|date|after:date',
            'recurrence_count' => 'nullable|integer|min:1|max:365',
            'update_series' => 'boolean',
        ];

        if (!$event) {
            $rules['date'] = 'required|date|after:now';
        }

        $validated = $request->validate($rules);

        if ($request->boolean('is_exclusive')) {
            if (empty($validated['department']) && empty($validated['allowed_departments'])) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'department' => 'For exclusive events, you must specify at least one department or select allowed departments.'
                ]);
            }
        }

        return $validated;
    }

    public function storeFeedback(Request $request, Event $event)
    {
        $request->validate([
            'feedback' => 'required|string|max:1000',
            'rating' => 'nullable|integer|min:1|max:5',
        ]);

        $feedback = Feedback::create([
            'event_id' => $event->id,
            'user_id' => Auth::id(),
            'feedback' => $request->feedback,
            'rating' => $request->rating,
        ]);

        $mailer = new PHPMailerService();

        $adminEmail = env('FEEDBACK_RECIPIENT', 'events.org.com');
        $userEmail = Auth::user()->email;

        $subject = "New Feedback for: {$event->title}";
        $body = "
            <h3>New Feedback Received</h3>
            <p><strong>Event:</strong> {$event->title}</p>
            <p><strong>Event Token:</strong> {$event->token}</p>
            <p><strong>User:</strong> {$userEmail}</p>
            <p><strong>Rating:</strong> {$request->rating}</p>
            <p><strong>Feedback:</strong><br>{$request->feedback}</p>
        ";

        $mailer->sendEmail($adminEmail, $subject, $body);

        $thankYouBody = "
            <h3>Thank You for Your Feedback!</h3>
            <p>We've received your feedback for <strong>{$event->title}</strong>.</p>
            <p>Your input helps us improve future events!</p>
        ";
        $mailer->sendEmail($userEmail, "Feedback Confirmation", $thankYouBody);

        return response()->json([
            'success' => true,
            'message' => 'Feedback submitted successfully and email sent!',
        ]);
    }

    private function processDepartmentExclusivity(array $validated, Request $request): array
    {
        if ($request->boolean('is_exclusive')) {
            $validated['is_exclusive'] = true;

            if (empty($validated['department']) && empty($validated['allowed_departments'])) {
                $validated['department'] = array_keys(self::DEPARTMENTS)[0];
            }
        } else {
            $validated['is_exclusive'] = false;
            $validated['department'] = null;
            $validated['allowed_departments'] = null;
        }
        return $validated;
    }
}