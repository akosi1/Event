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

        // ✅ Use 'joins' instead of 'registrations'
        $query = Event::with('joins.user')
            ->where('status', 'active')
            ->whereNull('parent_event_id');

        if ($request->filled('department')) {
            $query->forDepartment($request->department);
        } else {
            if ($user->department) {
                $query->forDepartment($user->department);
            }
        }

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

            // ✅ These methods already use 'joins', so they work
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