<?php

namespace App\Http\Controllers;

use App\Models\{Event, EventJoin, Notification};
use Illuminate\Http\Request;

class EventJoinController extends Controller
{
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
            'message' => 'Successfully joined the event!'
        ]);
    }

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
}