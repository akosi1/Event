<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{ProfileController, EventJoinController, DashboardController, MS365OTPController};

// Main welcome route
Route::get('/', fn() => view('welcome'));

// Authenticated routes with 'auth' and 'verified' middleware
Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard route
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Profile routes
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'edit'])->name('edit');
        Route::patch('/', [ProfileController::class, 'update'])->name('update');
        Route::delete('/', [ProfileController::class, 'destroy'])->name('destroy');
    });
    
    // Event join/leave routes
    Route::prefix('events/{event}')->name('events.')->group(function () {
        Route::post('join', [EventJoinController::class, 'join'])->name('join');
        Route::delete('leave', [EventJoinController::class, 'leave'])->name('leave');
    });
});

// MS365 Verification Routes (No middleware needed)
Route::get('ms365-verify', [MS365OTPController::class, 'showMS365Form'])->name('ms365.verify');

// Handle MS365 email verification and send OTP
Route::post('ms365-verify', [MS365OTPController::class, 'verifyMS365Account']);

// Show OTP verification form
Route::get('otp-verify', [MS365OTPController::class, 'showOTPForm'])->name('otp.verify.form');

// Handle OTP verification
Route::post('otp-verify', [MS365OTPController::class, 'verifyOTP']);

// Resend OTP request
Route::post('resend-otp', [MS365OTPController::class, 'resendOTP']);

// Include the authentication routes
require __DIR__.'/auth.php';
