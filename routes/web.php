<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    ProfileController,
    EventJoinController,
    DashboardController,
    Auth\MS365OTPController
};

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Welcome page (public)
Route::get('/', fn() => view('welcome'))->name('welcome');

/*
|--------------------------------------------------------------------------
| Guest Routes (MS365 & OTP Verification)
|--------------------------------------------------------------------------
| Accessible only to non-authenticated users
*/
Route::middleware('guest')->group(function () {
    // MS365 Email verification
    Route::get('ms365-verify', [MS365OTPController::class, 'showMS365Form'])->name('ms365.verify');
    Route::post('ms365-verify', [MS365OTPController::class, 'verifyMS365Account'])->name('ms365.verify.store');

    // OTP verification
    Route::get('otp-verify', [MS365OTPController::class, 'showOTPForm'])->name('otp.verify.form');
    Route::post('otp-verify', [MS365OTPController::class, 'verifyOTP'])->name('otp.verify.store');
    Route::post('otp-resend', [MS365OTPController::class, 'resendOTP'])->name('otp.resend');
});

/*
|--------------------------------------------------------------------------
| Authenticated Routes (require login + email verification)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard (this is your HOME route)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile management
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'edit'])->name('edit');
        Route::patch('/', [ProfileController::class, 'update'])->name('update');
        Route::delete('/', [ProfileController::class, 'destroy'])->name('destroy');
    });

    // Event participation
    Route::prefix('events/{event}')->name('events.')->group(function () {
        Route::post('join', [EventJoinController::class, 'join'])->name('join');
        Route::delete('leave', [EventJoinController::class, 'leave'])->name('leave');
    });
});

/*
|--------------------------------------------------------------------------
| Authentication Routes (login, logout, password reset, etc.)
|--------------------------------------------------------------------------
*/
require __DIR__.'/auth.php';