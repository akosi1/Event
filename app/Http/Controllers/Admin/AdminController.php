<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
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

        return view('admin.login'); // Make sure this view exists
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

    // =============== DASHBOARD & OTHER METHODS (unchanged below) ===============

    public function dashboard(Request $request)
    {
        $currentYear = Carbon::now()->year;
        
        $totalEvents = Event::count();
        $totalUsers = User::where('role', '!=', 'admin')->count();
        $totalAdmins = User::where('role', 'admin')->count();
        
        $monthlyEvents = Event::selectRaw('MONTHNAME(date) as month, COUNT(*) as count')
            ->whereYear('date', $currentYear)
            ->groupBy('month', DB::raw('MONTH(date)'))
            ->orderBy(DB::raw('MONTH(date)'))
            ->get();
        
        $locationData = Event::selectRaw('location, COUNT(*) as count')
            ->groupBy('location')
            ->orderBy('count', 'desc')
            ->get();
        
        $eventNamesData = Event::selectRaw('title, COUNT(*) as count')
            ->groupBy('title')
            ->orderBy('count', 'desc')
            ->limit(10)
            ->get();
        
        $perPage = $request->get('per_page', 5);
        $allEvents = Event::latest()->paginate($perPage, ['*'], 'page', $request->get('page', 1));
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
        return view('admin.certificates');
    }
}