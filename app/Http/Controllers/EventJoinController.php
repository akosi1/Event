<?php

namespace App\Http\Controllers;

use App\Models\{Event, EventJoin, Notification, PrintSummary};
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class EventJoinController extends Controller
{
    /**
     * Display event joins with filters
     */
    public function index(Request $request)
    {
        $query = EventJoin::with(['user', 'event', 'approvedBy']);

        if ($request->filled('event_id')) {
            $query->where('event_id', $request->event_id);
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('approved')) {
            $approved = $request->approved === '1';
            $query->where('approved', $approved);
        }

        if ($request->filled('search')) {
            $search = strtolower($request->search);
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', function ($q2) use ($search) {
                    $q2->whereRaw('LOWER(first_name) LIKE ?', ["%{$search}%"])
                       ->orWhereRaw('LOWER(last_name) LIKE ?', ["%{$search}%"])
                       ->orWhereRaw('LOWER(email) LIKE ?', ["%{$search}%"]);
                })->orWhereHas('event', function ($q2) use ($search) {
                    $q2->whereRaw('LOWER(title) LIKE ?', ["%{$search}%"]);
                });
            });
        }

        $eventJoins = $query->orderBy('joined_at', 'desc')->paginate(15);
        $events = Event::orderBy('date', 'asc')->get();
        $printSettings = PrintSummary::first();

        return view('admin.event-joins.index', compact('eventJoins', 'events', 'printSettings'));
    }

    /**
     * Generate and display print summary
     */
    public function print(Request $request)
    {
        $query = EventJoin::with(['user', 'event', 'approvedBy']);

        if ($request->filled('event_id')) {
            $query->where('event_id', $request->event_id);
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('approved')) {
            $approved = $request->approved === '1';
            $query->where('approved', $approved);
        }

        if ($request->filled('search')) {
            $search = strtolower($request->search);
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', function ($q2) use ($search) {
                    $q2->whereRaw('LOWER(first_name) LIKE ?', ["%{$search}%"])
                       ->orWhereRaw('LOWER(last_name) LIKE ?', ["%{$search}%"])
                       ->orWhereRaw('LOWER(email) LIKE ?', ["%{$search}%"]);
                })->orWhereHas('event', function ($q2) use ($search) {
                    $q2->whereRaw('LOWER(title) LIKE ?', ["%{$search}%"]);
                });
            });
        }

        $eventJoins = $query->orderBy('joined_at', 'desc')->get();
        $summaryData = PrintSummary::generateEventJoinsSummary($eventJoins);

        return view('admin.event-joins.print', compact('summaryData'));
    }

    /**
     * Update print settings (store images as Base64)
     */
    public function updatePrintSettings(Request $request)
    {
        $request->validate([
            'left_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'right_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'description' => 'nullable|string|max:500',
        ]);

        $settings = PrintSummary::firstOrCreate([]);

        // Convert uploaded images to Base64
        if ($request->hasFile('left_logo')) {
            $leftLogo = $request->file('left_logo');
            $settings->left_logo_base64 = $this->convertToBase64($leftLogo);
        }

        if ($request->hasFile('right_logo')) {
            $rightLogo = $request->file('right_logo');
            $settings->right_logo_base64 = $this->convertToBase64($rightLogo);
        }

        if ($request->filled('description')) {
            $settings->description = $request->description;
        }

        $settings->save();

        return redirect()->back()->with('success', 'Print settings updated successfully!');
    }

    /**
     * Helper: Convert uploaded image to Base64 string
     */
    private function convertToBase64($file)
    {
        $imageData = file_get_contents($file->getRealPath());
        $mimeType = $file->getMimeType();
        return 'data:' . $mimeType . ';base64,' . base64_encode($imageData);
    }

    /**
     * User joins an event
     */
    public function join(Request $request, Event $event)
    {
        $user = auth()->user();

        if ($event->isJoinedByUser($user->id)) {
            return response()->json(['success' => false, 'message' => 'You have already joined this event.']);
        }

        if (!$event->isAvailableForUserDepartment($user->department)) {
            return response()->json([
                'success' => false,
                'message' => 'This event is not available for your department (' . $user->getDepartmentNameAttribute() . ').'
            ]);
        }

        if ($event->status !== 'active') {
            return response()->json(['success' => false, 'message' => 'This event is not available for joining.']);
        }

        if ($event->date < now()) {
            return response()->json(['success' => false, 'message' => 'Cannot join past events.']);
        }

        EventJoin::create([
            'user_id' => $user->id,
            'event_id' => $event->id,
            'joined_at' => now()
        ]);

        Notification::create([
            'type' => 'event_join',
            'message' => $user->full_name . ' (' . $user->department . ') joined "' . $event->title . '"',
            'data' => [
                'user_id' => $user->id,
                'event_id' => $event->id,
                'user_name' => $user->full_name,
                'user_department' => $user->department,
                'event_title' => $event->title
            ]
        ]);

        return response()->json(['success' => true, 'message' => 'Successfully joined the event. Pending for Approval!']);
    }

    /**
     * User leaves an event
     */
    public function leave(Request $request, Event $event)
    {
        $user = auth()->user();

        $join = EventJoin::where('user_id', $user->id)
                         ->where('event_id', $event->id)
                         ->first();

        if (!$join) {
            return response()->json(['success' => false, 'message' => 'You have not joined this event.']);
        }

        if ($event->date < now()) {
            return response()->json(['success' => false, 'message' => 'Cannot leave past events.']);
        }

        $join->delete();

        Notification::create([
            'type' => 'event_leave',
            'message' => $user->full_name . ' (' . $user->department . ') left "' . $event->title . '"',
            'data' => [
                'user_id' => $user->id,
                'event_id' => $event->id,
                'user_name' => $user->full_name,
                'user_department' => $user->department,
                'event_title' => $event->title
            ]
        ]);

        return response()->json(['success' => true, 'message' => 'Successfully left the event!']);
    }

    /**
     * Approve event join request
     */
    public function approve(Request $request, $id)
    {
        $this->authorize('approve-eventjoin');

        $eventJoin = EventJoin::findOrFail($id);
        $eventJoin->approve(auth()->user());

        return redirect()->back()->with('success', 'Event join approved successfully.');
    }

    /**
     * Reject event join request
     */
    public function reject(EventJoin $eventJoin)
    {
        $this->authorize('approve-eventjoin');

        if (!$eventJoin->approved) {
            $eventJoin->reject(auth()->user());
            return redirect()->back()->with('success', 'Event join rejected successfully.');
        }

        return redirect()->back()->with('error', 'Cannot reject an already approved event join.');
    }
}
