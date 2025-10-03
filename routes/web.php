<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{ProfileController, EventJoinController, DashboardController, Auth\MS365OTPController};

Route::get('/', fn() => view('welcome'));

// Routes protected by auth and email verification middleware
Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard
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

// MS365 Email verification routes
Route::get('ms365-verify', [MS365OTPController::class, 'showMS365Form'])->name('ms365.verify');
Route::post('ms365-verify', [MS365OTPController::class, 'verifyMS365Account']);

// OTP verification routes
Route::get('otp-verify', [MS365OTPController::class, 'showOTPForm'])->name('otp.verify.form');
Route::post('otp-verify', [MS365OTPController::class, 'verifyOTP']);
Route::post('resend-otp', [MS365OTPController::class, 'resendOTP']);

// Default auth routes
require __DIR__.'/auth.php';
