<?php

namespace App\Http\Controllers;

use App\Models\{Event, EventJoin, Notification, PrintSummary};
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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
        
        // Get print settings for the modal
        $printSettings = PrintSummary::first();

        return view('admin.event-joins.index', compact('eventJoins', 'events', 'printSettings'));
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
        
        // Generate summary data with statistics
        $summaryData = PrintSummary::generateEventJoinsSummary($eventJoins);

        return view('admin.event-joins.print', compact('summaryData'));
    }

    /**
     * Update print settings (logos and description)
     */
    public function updatePrintSettings(Request $request)
    {
        $request->validate([
            'left_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'right_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'description' => 'nullable|string|max:500',
        ]);

        // Get or create print settings
        $settings = PrintSummary::firstOrCreate([]);

        // Handle left logo upload
        if ($request->hasFile('left_logo')) {
            // Delete old logo if exists
            if ($settings->left_logo_path) {
                $oldPath = public_path('storage/logos/' . $settings->left_logo_path);
                if (file_exists($oldPath)) {
                    @unlink($oldPath);
                }
            }

            $leftLogo = $request->file('left_logo');
            $leftLogoName = 'left_logo_' . time() . '_' . uniqid() . '.' . $leftLogo->extension();
            
            // Create directory if it doesn't exist
            if (!file_exists(public_path('storage/logos'))) {
                mkdir(public_path('storage/logos'), 0755, true);
            }
            
            $leftLogo->move(public_path('storage/logos'), $leftLogoName);
            $settings->left_logo_path = $leftLogoName;
        }

        // Handle right logo upload (SAIL logo)
        if ($request->hasFile('right_logo')) {
            // Delete old logo if exists
            if ($settings->right_logo_path) {
                $oldPath = public_path('storage/logos/' . $settings->right_logo_path);
                if (file_exists($oldPath)) {
                    @unlink($oldPath);
                }
            }

            $rightLogo = $request->file('right_logo');
            $rightLogoName = 'right_logo_' . time() . '_' . uniqid() . '.' . $rightLogo->extension();
            
            // Create directory if it doesn't exist
            if (!file_exists(public_path('storage/logos'))) {
                mkdir(public_path('storage/logos'), 0755, true);
            }
            
            $rightLogo->move(public_path('storage/logos'), $rightLogoName);
            $settings->right_logo_path = $rightLogoName;
        }

        // Update description
        if ($request->filled('description')) {
            $settings->description = $request->description;
        }

        $settings->save();

        return redirect()->back()->with('success', 'Print settings updated successfully!');
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
                         ->where('event_id', $event->id)
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