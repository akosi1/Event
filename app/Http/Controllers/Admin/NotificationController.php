<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Notification::recent()->paginate(10);
        return response()->json($notifications);
    }

    public function getUnreadCount()
    {
        $count = Notification::unread()->count();
        return response()->json(['count' => $count]);
    }

    public function markAsRead(Notification $notification)
    {
        $notification->markAsRead();
        return response()->json(['success' => true]);
    }

    public function markAllAsRead()
    {
        Notification::unread()->update(['is_read' => true]);
        return response()->json(['success' => true]);
    }

    public function destroy(Notification $notification)
    {
        $notification->delete();
        return response()->json(['success' => true]);
    }

    public function getLatest()
    {
        $notifications = Notification::recent()->limit(10)->get();
        return response()->json($notifications);
    }
}