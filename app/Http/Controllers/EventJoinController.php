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

        // Check if user can join this event based on year level restrictions
        if (!$event->isAvailableForYearLevel($user->year_level)) {
            return response()->json([
                'success' => false,
                'message' => 'This event is not available for your year level.'
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
     * User leaves an event (works for both approved and pending joins)
     */
    public function leave(Request $request, Event $event)
    {
        $user = auth()->user();

        // Find the join record - works for both approved and pending
        $join = EventJoin::where('user_id', $user->id)
                         ->where('event_id', $event->id)
                         ->first();

        if (!$join) {
            return response()->json([
                'success' => false,
                'message' => 'You have not joined this event.'
            ]);
        }

        // Store approval status before deletion for notification message
        $wasApproved = $join->approved;

        // Delete the join record
        $join->delete();

        // Create notification for admin about leaving/cancellation
        $actionType = $wasApproved ? 'left' : 'cancelled their request for';
        $notificationType = $wasApproved ? 'event_leave' : 'event_cancel';
        
        Notification::create([
            'type' => $notificationType,
            'message' => $user->full_name . ' (' . $user->department . ') ' . $actionType . ' "' . $event->title . '"',
            'data' => [
                'user_id' => $user->id,
                'event_id' => $event->id,
                'user_name' => $user->full_name,
                'user_department' => $user->department,
                'event_title' => $event->title,
                'was_approved' => $wasApproved
            ]
        ]);

        $message = $wasApproved 
            ? 'Successfully left the event!' 
            : 'Successfully cancelled your join request!';

        return response()->json([
            'success' => true,
            'message' => $message
        ]);
    }

    /**
     * Approve event join request
     */
    public function approve(Request $request, $id)
    {
        try {
            $this->authorize('approve-eventjoin');

            $eventJoin = EventJoin::with(['user', 'event'])->findOrFail($id);
            
            // Check if already approved
            if ($eventJoin->approved) {
                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'This event join has already been approved.'
                    ], 400);
                }
                
                return redirect()->route('admin.event-joins.index')
                                ->with('error', 'This event join has already been approved.');
            }

            // Approve the join
            $eventJoin->approve(auth()->user());

            // Create notification for user
            Notification::create([
                'user_id' => $eventJoin->user_id,
                'type' => 'event_join_approved',
                'message' => 'Your request to join "' . $eventJoin->event->title . '" has been approved!',
                'data' => [
                    'event_id' => $eventJoin->event_id,
                    'event_title' => $eventJoin->event->title,
                    'event_date' => $eventJoin->event->date->format('Y-m-d'),
                    'approved_by' => auth()->user()->full_name,
                    'approved_at' => now()->toDateTimeString()
                ]
            ]);

            // If it's an AJAX request, return JSON
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Event join approved successfully.'
                ]);
            }

            // Otherwise redirect with flash message
            return redirect()->route('admin.event-joins.index')
                            ->with('success', 'Event join approved successfully.');
                            
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'You do not have permission to approve event joins.'
                ], 403);
            }
            
            return redirect()->route('admin.event-joins.index')
                            ->with('error', 'You do not have permission to approve event joins.');
                            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Event join request not found.'
                ], 404);
            }
            
            return redirect()->route('admin.event-joins.index')
                            ->with('error', 'Event join request not found.');
                            
        } catch (\Exception $e) {
            \Log::error('Event join approval failed: ' . $e->getMessage());
            
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to approve: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()
                            ->with('error', 'Failed to approve event join: ' . $e->getMessage());
        }
    }

    /**
     * Reject event join request
     */
    public function reject(Request $request, $id)
    {
        try {
            $this->authorize('approve-eventjoin');

            $eventJoin = EventJoin::with(['user', 'event'])->findOrFail($id);

            // Check if already approved
            if ($eventJoin->approved) {
                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Cannot reject an already approved event join.'
                    ], 400);
                }
                
                return redirect()->route('admin.event-joins.index')
                                ->with('error', 'Cannot reject an already approved event join.');
            }

            // Store event details before deletion
            $userName = $eventJoin->user->full_name;
            $eventTitle = $eventJoin->event->title;
            $userId = $eventJoin->user_id;
            $eventId = $eventJoin->event_id;

            // Delete the join request (rejection means removing the request)
            $eventJoin->delete();

            // Create notification for user about rejection
            Notification::create([
                'user_id' => $userId,
                'type' => 'event_join_rejected',
                'message' => 'Your request to join "' . $eventTitle . '" has been rejected.',
                'data' => [
                    'event_id' => $eventId,
                    'event_title' => $eventTitle,
                    'rejected_by' => auth()->user()->full_name,
                    'rejected_at' => now()->toDateTimeString()
                ]
            ]);

            // If it's an AJAX request, return JSON
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Event join rejected successfully.'
                ]);
            }

            return redirect()->route('admin.event-joins.index')
                            ->with('success', 'Event join rejected successfully.');
                            
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'You do not have permission to reject event joins.'
                ], 403);
            }
            
            return redirect()->route('admin.event-joins.index')
                            ->with('error', 'You do not have permission to reject event joins.');
                            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Event join request not found.'
                ], 404);
            }
            
            return redirect()->route('admin.event-joins.index')
                            ->with('error', 'Event join request not found.');
                            
        } catch (\Exception $e) {
            \Log::error('Event join rejection failed: ' . $e->getMessage());
            
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to reject: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()
                            ->with('error', 'Failed to reject event join: ' . $e->getMessage());
        }
    }
}