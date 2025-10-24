<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Certificate;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        $query = Event::where('status', 'active')
                      ->whereNull('parent_event_id'); 

        // Department filter - now handles exclusive events properly
        if ($request->filled('department')) {
            $query->forDepartment($request->department);
        } else {
            // If no department filter, show events available for user's department
            if ($user->department) {
                $query->forDepartment($user->department);
            }
        }

        // Search filter
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%')
                  ->orWhere('location', 'like', '%' . $request->search . '%');
            });
        }

        $events = $query->orderBy('date', 'asc')->paginate(12);

        // Add is_joined attribute for each event
        $events->getCollection()->transform(function ($event) {
            $userId = auth()->id();
            $eventDate = Carbon::parse($event->date)->format('Y-m-d');
            $endTime = Carbon::parse($event->end_time)->format('H:i:s');
            $end = Carbon::parse("{$eventDate} {$endTime}", 'Asia/Manila');
            
            $event->is_joined = $event->isJoinedByUser(auth()->id());
            $event->join_status = $event->joinStatus($userId);
            $event->hasEnded = $end->isPast();

            return $event;
        });

        $certificates = Certificate::with('event')
        ->where('user_id', $user->id)
        ->get();

        return view('dashboard', compact('events', 'certificates'));
    }
}
