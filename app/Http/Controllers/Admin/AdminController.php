<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminController extends Controller
{
    public function dashboard(Request $request)
    {
        $currentYear = Carbon::now()->year;
        
        // Get total counts
        $totalEvents = Event::count();
        $totalUsers = User::where('role', '!=', 'admin')->count(); // Exclude admins from user count
        $totalAdmins = User::where('role', 'admin')->count(); // Count users with admin role
        
        // Get monthly events data for bar chart
        $monthlyEvents = Event::selectRaw('MONTHNAME(date) as month, COUNT(*) as count')
            ->whereYear('date', $currentYear)
            ->groupBy('month', DB::raw('MONTH(date)'))
            ->orderBy(DB::raw('MONTH(date)'))
            ->get();
        
        // Get location data for pie chart
        $locationData = Event::selectRaw('location, COUNT(*) as count')
            ->groupBy('location')
            ->orderBy('count', 'desc')
            ->get();
        
        // Get event names data for line chart
        $eventNamesData = Event::selectRaw('title, COUNT(*) as count')
            ->groupBy('title')
            ->orderBy('count', 'desc')
            ->limit(10) // Limit to top 10 event names
            ->get();
        
        // Get recent events with pagination
        $perPage = $request->get('per_page', 5); // Default to 5 items per page
        $allEvents = Event::latest()->paginate($perPage, ['*'], 'page', $request->get('page', 1));
        
        // Append query parameters to pagination links
        $allEvents->appends($request->query());
        
        return view('admin.dashboard', compact(
            'totalEvents',
            'totalUsers', 
            'totalAdmins',
            'currentYear',
            'monthlyEvents',
            'locationData',
            'eventNamesData',
            'allEvents',
            'perPage'
        ));
    }
    
    public function allEvents()
    {
        $events = Event::latest()->paginate(10);
        return view('admin.events.index', compact('events'));
    }
    
    public function certificates()
    {
        // Your existing certificates method
        return view('admin.certificates');
    }
}