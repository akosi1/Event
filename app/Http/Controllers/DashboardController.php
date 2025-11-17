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

        // Query active events
        $query = Event::with('joins.user')
            ->where('status', 'active')
            ->whereNull('parent_event_id');

        // ✅ FILTER OUT PAST EVENTS - Only show upcoming and ongoing events
        $query->where(function ($q) {
            $now = Carbon::now('Asia/Manila');
            
            $q->where('date', '>', $now->toDateString())
              ->orWhere(function ($subQ) use ($now) {
                  // Include events happening today that haven't ended yet
                  $subQ->whereDate('date', '=', $now->toDateString())
                       ->where(function ($timeQ) use ($now) {
                           $timeQ->whereNull('end_time')
                                 ->orWhereTime('end_time', '>=', $now->toTimeString());
                       });
              });
        });

        // Filter by department
        if ($request->filled('department')) {
            $query->forDepartment($request->department);
        } else {
            if ($user->department) {
                $query->forDepartment($user->department);
            }
        }

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%");
            });
        }

        $events = $query->orderBy('date', 'asc')
                        ->orderBy('start_time', 'asc')
                        ->paginate(12);

        $events->getCollection()->transform(function ($event) use ($user) {
            $eventDate = $event->date ? Carbon::parse($event->date)->format('Y-m-d') : null;
            $endTime = $event->end_time ? Carbon::parse($event->end_time)->format('H:i:s') : '23:59:59';

            if ($eventDate) {
                $end = Carbon::createFromFormat('Y-m-d H:i:s', "{$eventDate} {$endTime}", 'Asia/Manila');
                $event->hasEnded = $end->isPast();
            } else {
                $event->hasEnded = true;
            }

            $event->is_joined = $event->isJoinedByUser($user->id);
            $event->join_status = $event->joinStatus($user->id);

            return $event;
        });

        $certificates = Certificate::with('event')
            ->where('user_id', $user->id)
            ->get();

        return view('dashboard', compact('events', 'certificates'));
    }
}