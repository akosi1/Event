<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LoginLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LoginLogsController extends Controller
{
    /**
     * Display login logs with map and filters.
     */
    public function index(Request $request)
    {
        $query = LoginLog::with('user')->orderBy('created_at', 'desc');

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Search by email, IP, or user name
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('email_attempted', 'like', "%{$search}%")
                  ->orWhere('ip_address', 'like', "%{$search}%")
                  ->orWhere('city', 'like', "%{$search}%")
                  ->orWhere('country', 'like', "%{$search}%")
                  ->orWhereHas('user', function($q) use ($search) {
                      $q->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by session status (active/ended)
        if ($request->filled('session_status')) {
            if ($request->session_status === 'active') {
                $query->whereNull('logout_at')->where('status', 'success');
            } elseif ($request->session_status === 'ended') {
                $query->whereNotNull('logout_at');
            }
        }

        $logs = $query->paginate(50);

        // Get comprehensive statistics
        $stats = [
            'total' => LoginLog::count(),
            'success' => LoginLog::where('status', 'success')->count(),
            'failed' => LoginLog::where('status', 'failed')->count(),
            'locked_out' => LoginLog::where('status', 'locked_out')->count(),
            'active_sessions' => LoginLog::where('status', 'success')->whereNull('logout_at')->count(),
            'today' => LoginLog::whereDate('created_at', today())->count(),
            'this_week' => LoginLog::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'this_month' => LoginLog::whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count(),
        ];

        // Get map data (only logs with valid coordinates)
        $mapData = LoginLog::with('user')
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->orderBy('created_at', 'desc')
            ->limit(500)
            ->get()
            ->map(fn($log) => $log->map_marker_data);

        // Get top countries
        $topCountries = LoginLog::select('country', 'country_code', DB::raw('count(*) as total'))
            ->whereNotNull('country')
            ->groupBy('country', 'country_code')
            ->orderBy('total', 'desc')
            ->limit(10)
            ->get();

        // Get top cities
        $topCities = LoginLog::select('city', 'country', DB::raw('count(*) as total'))
            ->whereNotNull('city')
            ->groupBy('city', 'country')
            ->orderBy('total', 'desc')
            ->limit(10)
            ->get();

        // Get recent activity by day (last 30 days)
        $activityChart = LoginLog::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as total'),
                DB::raw('SUM(CASE WHEN status = "success" THEN 1 ELSE 0 END) as success'),
                DB::raw('SUM(CASE WHEN status = "failed" THEN 1 ELSE 0 END) as failed'),
                DB::raw('SUM(CASE WHEN status = "locked_out" THEN 1 ELSE 0 END) as locked_out')
            )
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        // Get device statistics
        $deviceStats = LoginLog::select('device_type', DB::raw('count(*) as total'))
            ->whereNotNull('device_type')
            ->groupBy('device_type')
            ->orderBy('total', 'desc')
            ->get();

        // Get browser statistics
        $browserStats = LoginLog::select('browser', DB::raw('count(*) as total'))
            ->whereNotNull('browser')
            ->groupBy('browser')
            ->orderBy('total', 'desc')
            ->limit(10)
            ->get();

        // Get OS statistics
        $osStats = LoginLog::select('os', DB::raw('count(*) as total'))
            ->whereNotNull('os')
            ->groupBy('os')
            ->orderBy('total', 'desc')
            ->limit(10)
            ->get();

        return view('admin.login-logs.index', compact(
            'logs',
            'stats',
            'mapData',
            'topCountries',
            'topCities',
            'activityChart',
            'deviceStats',
            'browserStats',
            'osStats'
        ));
    }

    /**
     * Get login logs data for AJAX requests (for map updates).
     */
    public function getData(Request $request)
    {
        $logs = LoginLog::with('user')
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->orderBy('created_at', 'desc')
            ->limit(500)
            ->get()
            ->map(fn($log) => $log->map_marker_data);

        return response()->json($logs);
    }

    /**
     * Show detailed log information.
     */
    public function show(LoginLog $loginLog)
    {
        $loginLog->load('user');
        return view('admin.login-logs.show', compact('loginLog'));
    }

    /**
     * Delete old login logs.
     */
    public function cleanup(Request $request)
    {
        $request->validate([
            'days' => 'required|integer|min:1|max:365',
        ]);

        $deleted = LoginLog::where('created_at', '<', now()->subDays($request->days))->delete();

        return back()->with('success', "Deleted {$deleted} login logs older than {$request->days} days.");
    }
}