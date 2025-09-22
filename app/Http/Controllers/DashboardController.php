<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        
        $query = Event::where('status', 'active')
                      ->upcoming()
                      ->whereNull('parent_event_id'); // Only show parent events, not recurring instances

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
            $event->is_joined = $event->isJoinedByUser(auth()->id());
            return $event;
        });

        return view('dashboard', compact('events'));
    }
}