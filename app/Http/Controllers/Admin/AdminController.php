<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Event, Certificate, User, EventJoin};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, DB, Hash, Storage};
use Symfony\Component\HttpFoundation\StreamedResponse;
use Carbon\Carbon;

class AdminController extends Controller
{
    // Show admin login form
    public function showLoginForm()
    {
        // If already logged in, redirect to dashboard
        if (Auth::guard('admin')->check()) {
            return redirect()->route('admin.dashboard');
        }

        return view('admin.login');
    }

    // Handle admin login
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');

        // Use 'admin' guard (ensure it's configured in config/auth.php)
        if (Auth::guard('admin')->attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended(route('admin.dashboard'));
        }

        // If login fails, redirect back with error
        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->withInput();
    }

    // Handle admin logout
    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }

    // =============== DASHBOARD METHOD - UPDATED ===============

    public function dashboard(Request $request)
    {
        $currentYear = Carbon::now()->year;
        $perPage = $request->get('per_page', 5);

        // Basic stats
        $totalEvents = Event::count();
        $totalUsers = User::where('role', '!=', 'admin')->count();
        $totalAdmins = User::where('role', 'admin')->count();

        // Monthly events data
        $monthlyEvents = Event::select(
            DB::raw('MONTH(date) as month_num'),
            DB::raw('MONTHNAME(date) as month'),
            DB::raw('COUNT(*) as count')
        )
        ->whereYear('date', $currentYear)
        ->groupBy('month_num', 'month', DB::raw('MONTH(date)'))
        ->orderBy(DB::raw('MONTH(date)'))
        ->get();

        // Location data
        $locationData = Event::select('location', DB::raw('COUNT(*) as count'))
            ->groupBy('location')
            ->orderByDesc('count')
            ->limit(7)
            ->get();

        // Event names data
        $eventNamesData = Event::select('title', DB::raw('COUNT(*) as count'))
            ->groupBy('title')
            ->orderByDesc('count')
            ->limit(10)
            ->get();
        // Event Joins Status Data (NEW)
        $eventJoinsStatusData = [
            'pending' => EventJoin::where('approved', false)->count(),
            'approved' => EventJoin::where('approved', true)->count()
        ];

        // Monthly Event Joins Data (NEW)
        $monthlyEventJoins = EventJoin::select(
            DB::raw('MONTH(joined_at) as month_num'),
            DB::raw('MONTHNAME(joined_at) as month'),
            DB::raw('SUM(CASE WHEN approved = 1 THEN 1 ELSE 0 END) as approved'),
            DB::raw('SUM(CASE WHEN approved = 0 THEN 1 ELSE 0 END) as pending')
        )
        ->whereYear('joined_at', $currentYear)
        ->groupBy('month_num', 'month', DB::raw('MONTH(joined_at)'))
        ->orderBy(DB::raw('MONTH(joined_at)'))
        ->get();

        // Top Events by Join Count (NEW)
        $topEventsByJoins = Event::select('events.id', 'events.title', DB::raw('COUNT(event_joins.id) as join_count'))
            ->leftJoin('event_joins', 'events.id', '=', 'event_joins.event_id')
            ->groupBy('events.id', 'events.title')
            ->orderByDesc('join_count')
            ->limit(10)
            ->get();

        // Recent events with pagination
        $allEvents = Event::latest('date')->paginate($perPage);
        $allEvents->appends($request->query());

        return view('admin.dashboard', compact(
            'totalEvents',
            'totalUsers',
            'totalAdmins',
            'currentYear',
            'monthlyEvents',
            'locationData',
            'eventNamesData',
            'eventJoinsStatusData',
            'monthlyEventJoins',
            'topEventsByJoins',
            'allEvents',
            'perPage'
        ));
    }

    // =============== OTHER METHODS (unchanged) ===============

    public function allEvents()
    {
        $events = Event::latest()->paginate(10);
        return view('admin.events.index', compact('events'));
    }

    public function certificates(Request $request)
    {
        $query = Certificate::with(['user', 'event']);

        if ($search = $request->input('search')) {
            $query->whereHas('user', function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                ->orWhere('last_name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%");
            })->orWhereHas('event', function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%");
            })->orWhere('id', $search); // certificate ID
        }

        // Filter by Event
        if ($eventId = $request->input('event_id')) {
            $query->where('event_id', $eventId);
        }

        // Filter by Department (if user has department field)
        if ($department = $request->input('department')) {
            $query->whereHas('user', function($q) use ($department) {
                $q->where('department', $department);
            });
        }

        $certificates = $query->orderBy('created_at', 'desc')->paginate(10);
        $events_list = Event::pluck('title', 'id');

        return view('admin.certificates', compact('certificates', 'events_list'));
    }

    public function download($id)
    {
        $certificate = Certificate::with('user', 'event')->findOrFail($id);

        if (!$certificate->certificate_path || !Storage::exists($certificate->certificate_path)) {
            return redirect()->back()->with('error', 'Certificate file not found.');
        }

        $firstName = strtolower(trim($certificate->user->first_name));
        $eventTitle = strtolower(trim(preg_replace('/[^A-Za-z0-9\-]/', '_', $certificate->event->title ?? 'event')));
        $fileName = $firstName . '_' . $eventTitle . '_certificate.jpg';

        return Storage::download($certificate->certificate_path, $fileName);
    }
}