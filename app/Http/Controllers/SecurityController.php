<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SecurityController extends Controller
{
    public function logAttempt(Request $request)
    {
        Log::warning('Security attempt: ' . $request->input('type'));
        return response()->json(['status' => 'logged']);
    }
}