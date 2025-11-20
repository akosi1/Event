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

    const YEAR_LEVELS = [
        '1' => '1st Year',
        '2' => '2nd Year',
        '3' => '3rd Year',
        '4' => '4th Year'
    ];

    public function index(Request $request)
    {
        $query = Event::with(['parentEvent', 'childEvents'])->whereNull('parent_event_id');

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

        if ($request->filled('year_level')) {
            $query->whereJsonContains('allowed_year_levels', $request->year_level);
        }

        if ($request->filled('exclusivity')) {
            $query->where('is_exclusive', $request->exclusivity === 'exclusive');
        }

        if ($request->filled('recurrence')) {
            $query->where('is_recurring', $request->recurrence === 'recurring');
        }

        $perPage = $request->get('per_page', 10);
        $events = $query->orderBy('created_at', 'desc')->paginate($perPage);
        $events->appends($request->query());

        return view('admin.events.index', compact('events'));
    }

    public function archive(Request $request)
    {
        $query = Event::onlyTrashed()->with(['parentEvent', 'childEvents'])->whereNull('parent_event_id');

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%')
                  ->orWhere('location', 'like', '%' . $request->search . '%');
            });
        }

        $perPage = $request->get('per_page', 10);
        $archivedEvents = $query->orderBy('deleted_at', 'desc')->paginate($perPage);
        $archivedEvents->appends($request->query());

        return view('admin.events.archive', compact('archivedEvents'));
    }

    public function restore($id)
    {
        try {
            $event = Event::onlyTrashed()->findOrFail($id);
            
            if ($event->isRecurring() && request()->boolean('restore_series')) {
                $event->childEvents()->onlyTrashed()->restore();
            }
            
            $event->restore();

            return redirect()->route('admin.events.archive')
                            ->with('success', 'Event "' . $event->title . '" restored successfully!');
        } catch (\Exception $e) {
            \Log::error('Event restoration failed: ' . $e->getMessage());
            return redirect()->route('admin.events.archive')
                            ->with('error', 'Failed to restore event: ' . $e->getMessage());
        }
    }

    public function forceDelete($id)
    {
        try {
            $event = Event::onlyTrashed()->findOrFail($id);
            
            if ($event->isRecurring() && request()->boolean('delete_series')) {
                $event->childEvents()->onlyTrashed()->forceDelete();
            }
            
            $event->deleteImage();
            $event->deleteCancellationDocument();
            
            $event->forceDelete();

            return redirect()->route('admin.events.archive')
                            ->with('success', 'Event "' . $event->title . '" permanently deleted!');
        } catch (\Exception $e) {
            \Log::error('Event permanent deletion failed: ' . $e->getMessage());
            return redirect()->route('admin.events.archive')
                            ->with('error', 'Failed to permanently delete event: ' . $e->getMessage());
        }
    }

    public function print(Request $request)
    {
        $query = Event::with(['parentEvent', 'childEvents', 'joinedUsers'])->whereNull('parent_event_id');

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

        if ($request->filled('year_level')) {
            $query->whereJsonContains('allowed_year_levels', $request->year_level);
        }

        if ($request->filled('exclusivity')) {
            $query->where('is_exclusive', $request->exclusivity === 'exclusive');
        }

        if ($request->filled('recurrence')) {
            $query->where('is_recurring', $request->recurrence === 'recurring');
        }

        $events = $query->orderBy('date', 'asc')->get();
        
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
        try {
            $validated = $this->validateEventData($request);

            // Handle image uploads (base64)
            foreach (['image', 'certificate_template_image'] as $field) {
                if ($request->filled($field)) {
                    $base64String = $request->input($field);

                    if (!preg_match('/^data:image\/(jpeg|png|jpg);base64,/', $base64String)) {
                        return back()->withErrors([$field => 'Invalid base64 image format.'])->withInput();
                    }

                    $imageSize = (strlen($base64String) * 3) / 4;
                    if ($imageSize > 2097152) {
                        return back()->withErrors([$field => 'Image size must not exceed 2MB.'])->withInput();
                    }

                    $validated[$field] = $base64String;
                }
            }

            // Handle cancellation document upload (BASE64)
            if ($request->filled('cancellation_document_base64')) {
                $base64Doc = $request->input('cancellation_document_base64');
                $docName = $request->input('cancellation_document_name');
                
                // Validate base64 format
                if (!preg_match('/^data:(application\/pdf|application\/vnd\.openxmlformats-officedocument\.wordprocessingml\.document);base64,/', $base64Doc, $matches)) {
                    return back()->withErrors(['cancellation_document' => 'Invalid document format. Only PDF and DOCX allowed.'])->withInput();
                }
                
                // Check size (5MB limit)
                $docSize = (strlen($base64Doc) * 3) / 4;
                if ($docSize > 5242880) {
                    return back()->withErrors(['cancellation_document' => 'Document size must not exceed 5MB.'])->withInput();
                }
                
                $validated['cancellation_document'] = $base64Doc;
                $validated['cancellation_document_name'] = $docName;
            }

            $validated = $this->processDepartmentExclusivity($validated, $request);

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
        } catch (\Exception $e) {
            \Log::error('Event creation failed: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Failed to create event: ' . $e->getMessage()])->withInput();
        }
    }

    public function show(Event $event)
    {
        $event->load([
            'childEvents' => function ($query) {
                $query->orderBy('date', 'asc');
            },
            'joinedUsers' => function ($query) {
                $query->select('users.id', 'users.first_name', 'users.last_name', 'users.department', 'users.email', 'users.year_level')
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

        $yearLevelStats = $event->joinedUsers->groupBy('year_level')->map(function ($users) {
            return [
                'count' => $users->count(),
                'users' => $users
            ];
        });

        return view('admin.events.show', compact('event', 'departmentStats', 'yearLevelStats'));
    }

    public function edit(Event $event)
    {
        return view('admin.events.edit', compact('event'));
    }

    public function update(Request $request, Event $event)
    {
        try {
            $validated = $this->validateEventData($request, $event);

            // Handle image removal
            if ($request->filled('remove_image') && $request->remove_image == '1') {
                $event->deleteImage();
                $validated['image'] = null;
            }

            // Handle certificate removal
            if ($request->filled('remove_certificate') && $request->remove_certificate == '1') {
                if ($event->certificate_template_image) {
                    $validated['certificate_template_image'] = null;
                }
            }

            // Handle document removal
            if ($request->filled('remove_cancellation_document') && $request->remove_cancellation_document == '1') {
                $event->deleteCancellationDocument();
                $validated['cancellation_document'] = null;
                $validated['cancellation_document_name'] = null;
            }

            // Handle new image uploads (base64)
            foreach (['image', 'certificate_template_image'] as $field) {
                if ($request->filled($field)) {
                    $base64String = $request->input($field);

                    if (!preg_match('/^data:image\/(jpeg|png|jpg);base64,/', $base64String)) {
                        return back()->withErrors([$field => 'Invalid base64 image format.'])->withInput();
                    }

                    $imageSize = (strlen($base64String) * 3) / 4;
                    if ($imageSize > 2097152) {
                        return back()->withErrors([$field => 'Image size must not exceed 2MB.'])->withInput();
                    }

                    $validated[$field] = $base64String;
                }
            }

            // Handle new document upload (BASE64)
            if ($request->filled('cancellation_document_base64')) {
                $base64Doc = $request->input('cancellation_document_base64');
                $docName = $request->input('cancellation_document_name');
                
                // Validate base64 format
                if (!preg_match('/^data:(application\/pdf|application\/vnd\.openxmlformats-officedocument\.wordprocessingml\.document);base64,/', $base64Doc)) {
                    return back()->withErrors(['cancellation_document' => 'Invalid document format. Only PDF and DOCX allowed.'])->withInput();
                }
                
                // Check size (5MB limit)
                $docSize = (strlen($base64Doc) * 3) / 4;
                if ($docSize > 5242880) {
                    return back()->withErrors(['cancellation_document' => 'Document size must not exceed 5MB.'])->withInput();
                }
                
                // Delete old document
                $event->deleteCancellationDocument();
                
                $validated['cancellation_document'] = $base64Doc;
                $validated['cancellation_document_name'] = $docName;
            }

            $validated = $this->processDepartmentExclusivity($validated, $request);

            unset($validated['remove_image']);
            unset($validated['remove_certificate']);
            unset($validated['remove_cancellation_document']);

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
        } catch (\Exception $e) {
            \Log::error('Event update failed: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Failed to update event: ' . $e->getMessage()])->withInput();
        }
    }

    public function destroy(Event $event)
    {
        try {
            if ($event->isRecurring() && request()->boolean('delete_series')) {
                $event->childEvents()->delete();
                $message = 'Event series "' . $event->title . '" deleted successfully! You can restore it from Archive.';
            } else {
                $message = 'Event "' . $event->title . '" deleted successfully! You can restore it from Archive.';
            }
            
            $event->delete();

            return redirect()->route('admin.events.index')->with('success', $message);
        } catch (\Exception $e) {
            \Log::error('Event deletion failed: ' . $e->getMessage());
            return redirect()->route('admin.events.index')->with('error', 'Failed to delete event: ' . $e->getMessage());
        }
    }

    public function showByToken(string $token)
    {
        $event = Event::findByToken($token);

        if (!$event) {
            abort(404, 'Event not found');
        }

        return $this->show($event);
    }

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

    public static function getYearLevels()
    {
        return self::YEAR_LEVELS;
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
            'certificate_template_image' => ['nullable', 'string', 'regex:/^data:image\/(jpeg|png|jpg);base64,/'],
            'cancellation_document_base64' => ['nullable', 'string', 'regex:/^data:(application\/pdf|application\/vnd\.openxmlformats-officedocument\.wordprocessingml\.document);base64,/'],
            'cancellation_document_name' => 'nullable|string|max:255',
            'remove_image' => 'nullable|boolean',
            'remove_certificate' => 'nullable|boolean',
            'remove_cancellation_document' => 'nullable|boolean',
            'is_exclusive' => 'boolean',
            'allowed_departments' => 'nullable|array',
            'allowed_departments.*' => 'string|in:' . implode(',', array_keys(self::DEPARTMENTS)),
            'allowed_year_levels' => 'nullable|array',
            'allowed_year_levels.*' => 'string|in:' . implode(',', array_keys(self::YEAR_LEVELS)),
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

        $user = Auth::user();
        $feedbackPreview = strlen($request->feedback) > 100 
            ? substr($request->feedback, 0, 100) . '...' 
            : $request->feedback;

        $message = "{$user->first_name} {$user->last_name} shared their thoughts on \"{$event->title}\"";
        
        if ($request->rating) {
            $message .= " (Rating: {$request->rating}/5)";
        }
        
        $message .= ": \"{$feedbackPreview}\"";

        \App\Models\Notification::create([
            'user_id' => null,
            'type' => 'feedback',
            'message' => $message,
            'data' => json_encode([
                'event_id' => $event->id,
                'event_title' => $event->title,
                'event_token' => $event->token,
                'user_id' => $user->id,
                'user_name' => "{$user->first_name} {$user->last_name}",
                'user_email' => $user->email,
                'feedback_id' => $feedback->id,
                'rating' => $request->rating,
                'feedback' => $request->feedback,
                'feedback_preview' => $feedbackPreview
            ]),
            'is_read' => false,
        ]);

        $mailer = new PHPMailerService();
        $adminEmail = env('FEEDBACK_RECIPIENT', 'events.org.com');
        $userEmail = $user->email;

        $subject = "New Feedback for: {$event->title}";
        $body = "
            <h3>New Feedback Received</h3>
            <p><strong>Event:</strong> {$event->title}</p>
            <p><strong>Event Token:</strong> {$event->token}</p>
            <p><strong>User:</strong> {$user->first_name} {$user->last_name} ({$userEmail})</p>
            <p><strong>Rating:</strong> " . ($request->rating ? "{$request->rating}/5" : "No rating") . "</p>
            <p><strong>Feedback:</strong><br>" . nl2br(htmlspecialchars($request->feedback)) . "</p>
        ";

        $mailer->sendEmail($adminEmail, $subject, $body);

        $thankYouBody = "
            <h3>Thank You for Your Feedback!</h3>
            <p>We've received your feedback for <strong>{$event->title}</strong>.</p>
            <p>Your input helps us improve future events!</p>
            <p><strong>Your feedback:</strong><br>" . nl2br(htmlspecialchars($request->feedback)) . "</p>
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
            $validated['allowed_year_levels'] = null;
        }
        return $validated;
    }
}