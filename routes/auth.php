<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\Auth\MS365OTPController;
use App\Http\Controllers\Auth\LoginWithVerificationController;
use App\Http\Controllers\DashboardController; // ← ADDED
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('ms365-verify', [MS365OTPController::class, 'showMS365Form'])->name('ms365.verify');
    Route::post('ms365-verify', [MS365OTPController::class, 'verifyMS365Account'])->middleware('throttle:5,1')->name('ms365.verify.store');
    Route::get('otp-verify', [MS365OTPController::class, 'showOTPForm'])->name('otp.verify.form');
    Route::post('otp-verify', [MS365OTPController::class, 'verifyOTP'])->middleware('throttle:5,1')->name('otp.verify.store');
    Route::post('otp-resend', [MS365OTPController::class, 'resendOTP'])->middleware('throttle:3,1')->name('otp.resend');

    Route::get('register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('register', [RegisteredUserController::class, 'store'])->middleware('throttle:5,1')->name('register.store');

    // 🔁 LOGIN WITH VERIFICATION
    Route::get('login', [LoginWithVerificationController::class, 'showLoginForm'])->name('login');
    Route::post('login', [LoginWithVerificationController::class, 'attemptLogin'])->middleware('throttle:5,1')->name('login.store');
    Route::post('login/verify-code', [LoginWithVerificationController::class, 'verifyCode'])->name('login.verify');

    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])->middleware('throttle:5,1')->name('password.email');
    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
    Route::post('reset-password', [NewPasswordController::class, 'store'])->middleware('throttle:5,1')->name('password.store');
});

Route::middleware('auth')->group(function () {
    Route::get('verify-email', EmailVerificationPromptController::class)->name('verification.notice');
    Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)->middleware(['signed', 'throttle:6,1'])->name('verification.verify');
    Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])->middleware('throttle:3,1')->name('verification.send');

    Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])->name('password.confirm');
    Route::post('confirm-password', [ConfirmablePasswordController::class, 'store'])->middleware('throttle:5,1')->name('password.confirm.store');

    Route::put('password', [PasswordController::class, 'update'])->middleware('throttle:5,1')->name('password.update');

    Route::post('logout', [LoginWithVerificationController::class, 'logout'])->name('logout');

    // ✅ CORRECT DASHBOARD ROUTE — uses controller
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});

Route::get('/', function () {
    return view('welcome');
})->name('home');