<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use App\Http\Controllers\{
    ProfileController,
    EventJoinController,
    DashboardController,
    EmailController,
    Auth\MS365OTPController
};

Route::get('/', fn() => view('welcome'));

// Routes protected by auth and email verification middleware
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'edit'])->name('edit');
        Route::patch('/', [ProfileController::class, 'update'])->name('update');
        Route::delete('/', [ProfileController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('events/{event}')->name('events.')->group(function () {
        Route::post('join', [EventJoinController::class, 'join'])->name('join');
        Route::delete('leave', [EventJoinController::class, 'leave'])->name('leave');
    });
});

//routes for ms365
Route::get('ms365-verify', [MS365OTPController::class, 'showMS365Form'])->name('ms365.verify');
Route::post('ms365-verify', [MS365OTPController::class, 'verifyMS365Account']);

Route::get('otp-verify', [MS365OTPController::class, 'showOTPForm'])->name('otp.verify.form');
Route::post('otp-verify', [MS365OTPController::class, 'verifyOTP']);
Route::post('resend-otp', [MS365OTPController::class, 'resendOTP']);

Route::get('/send-email', [EmailController::class, 'sendEmail'])->name('email.send');


require __DIR__.'/auth.php';
