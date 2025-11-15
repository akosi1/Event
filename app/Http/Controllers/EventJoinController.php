<?php

namespace App\Http\Controllers;

use App\Models\{Event, EventJoin, Notification};
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

        // Optional filters
        if ($request->filled('event_id')) {
            $query->where('event_id', $request->event_id);
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('approved')) {
            $approved = $request->approved === '1' ? true : false;
            $query->where('approved', $approved);
        }

        if ($request->filled('search')) {
            $search = strtolower($request->search);
            $query->whereHas('user', function ($q) use ($search) {
                $q->whereRaw('LOWER(first_name) LIKE ?', ["%{$search}%"])
                  ->orWhereRaw('LOWER(last_name) LIKE ?', ["%{$search}%"])
                  ->orWhereRaw('LOWER(email) LIKE ?', ["%{$search}%"]);
            })->orWhereHas('event', function ($q) use ($search) {
                $q->whereRaw('LOWER(title) LIKE ?', ["%{$search}%"]);
            });
        }

        $eventJoins = $query->orderBy('joined_at', 'desc')->paginate(15);
        $events = Event::orderBy('date', 'asc')->get();

        return view('admin.event-joins.index', compact('eventJoins', 'events'));
    }

    /**
     * Generate and display print summary
     */
    public function print(Request $request)
    {
        $query = EventJoin::with(['user', 'event', 'approvedBy']);

        // Apply same filters as index page
        if ($request->filled('event_id')) {
            $query->where('event_id', $request->event_id);
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('approved')) {
            $approved = $request->approved === '1' ? true : false;
            $query->where('approved', $approved);
        }

        if ($request->filled('search')) {
            $search = strtolower($request->search);
            $query->whereHas('user', function ($q) use ($search) {
                $q->whereRaw('LOWER(first_name) LIKE ?', ["%{$search}%"])
                  ->orWhereRaw('LOWER(last_name) LIKE ?', ["%{$search}%"])
                  ->orWhereRaw('LOWER(email) LIKE ?', ["%{$search}%"]);
            })->orWhereHas('event', function ($q) use ($search) {
                $q->whereRaw('LOWER(title) LIKE ?', ["%{$search}%"]);
            });
        }

        // Get all records for printing (no pagination)
        $eventJoins = $query->orderBy('joined_at', 'desc')->get();
        
        // Generate summary data
        $summaryData = [
            'event_joins' => $eventJoins,
            'total_records' => $eventJoins->count(),
            'approved_count' => $eventJoins->where('approved', true)->count(),
            'pending_count' => $eventJoins->where('approved', false)->count(),
            'generated_at' => now()->format('F d, Y h:i A'),
            'logo_left_path' => asset('images/logo.png'),
            'logo_right_path' => asset('images/Official-Logo-Seal-madridejos.png')
        ];

        return view('admin.event-joins.print', compact('summaryData'));
    }

    /**
     * User joins an event
     */
    public function join(Request $request, Event $event)
    {
        $user = auth()->user();

        // Check if user has already joined this event
        if ($event->isJoinedByUser($user->id)) {
            return response()->json([
                'success' => false,
                'message' => 'You have already joined this event.'
            ]);
        }

        // Check if user can join this event based on department restrictions
        if (!$event->isAvailableForUserDepartment($user->department)) {
            return response()->json([
                'success' => false,
                'message' => 'This event is not available for your department (' . $user->getDepartmentNameAttribute() . ').'
            ]);
        }

        // Check event status
        if ($event->status !== 'active') {
            return response()->json([
                'success' => false,
                'message' => 'This event is not available for joining.'
            ]);
        }

        // Check if event is in the past
        if ($event->date < now()) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot join past events.'
            ]);
        }

        EventJoin::create([
            'user_id' => $user->id,
            'event_id' => $event->id,
            'joined_at' => now()
        ]);

        // Create notification for admin
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

        return response()->json([
            'success' => true,
            'message' => 'Successfully joined the event. Pending for Approval!'
        ]);
    }

    /**
     * User leaves an event
     */
    public function leave(Request $request, Event $event)
    {
        $user = auth()->user();

        $join = EventJoin::where('user_id', $user->id)
                         ->where('event_id', $request->event_id)
                         ->first();

        if (!$join) {
            return response()->json([
                'success' => false,
                'message' => 'You have not joined this event.'
            ]);
        }

        // Check if event is in the past
        if ($event->date < now()) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot leave past events.'
            ]);
        }

        $join->delete();

        // Create notification for admin about leaving
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

        return response()->json([
            'success' => true,
            'message' => 'Successfully left the event!'
        ]);
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