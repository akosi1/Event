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
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Guest Routes (Unauthenticated Users)
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    
    /*
    |----------------------------------------------------------------------
    | MS365 Email Verification Flow
    |----------------------------------------------------------------------
    */
    // Step 1: Show MS365 email verification form
    Route::get('ms365-verify', [MS365OTPController::class, 'showMS365Form'])
        ->name('ms365.verify');
    
    // Step 2: Verify MS365 email and send OTP
    Route::post('ms365-verify', [MS365OTPController::class, 'verifyMS365Account'])
        ->name('ms365.verify.store');
    
    // Step 3: Show OTP verification form
    Route::get('otp-verify', [MS365OTPController::class, 'showOTPForm'])
        ->name('otp.verify.form');
    
    // Step 4: Verify OTP code
    Route::post('otp-verify', [MS365OTPController::class, 'verifyOTP'])
        ->name('otp.verify.store');
    
    // Resend OTP if needed
    Route::post('otp-resend', [MS365OTPController::class, 'resendOTP'])
        ->name('otp.resend');

    /*
    |----------------------------------------------------------------------
    | Registration Routes (Requires Verified Email)
    |----------------------------------------------------------------------
    */
    // Step 5: Show registration form (after OTP verification)
    Route::get('register', [RegisteredUserController::class, 'create'])
        ->name('register');

    // Step 6: Complete registration
    Route::post('register', [RegisteredUserController::class, 'store'])
        ->name('register.store');

    /*
    |----------------------------------------------------------------------
    | Login Routes
    |----------------------------------------------------------------------
    */
    Route::get('login', [AuthenticatedSessionController::class, 'create'])
        ->name('login');

    Route::post('login', [AuthenticatedSessionController::class, 'store'])
        ->name('login.store');

    /*
    |----------------------------------------------------------------------
    | Password Reset Routes
    |----------------------------------------------------------------------
    */
    // Show password reset request form
    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])
        ->name('password.request');

    // Send password reset link
    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])
        ->name('password.email');

    // Show password reset form
    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])
        ->name('password.reset');

    // Update password
    Route::post('reset-password', [NewPasswordController::class, 'store'])
        ->name('password.store');
});

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    
    /*
    |----------------------------------------------------------------------
    | Email Verification Routes
    |----------------------------------------------------------------------
    */
    // Email verification notice
    Route::get('verify-email', EmailVerificationPromptController::class)
        ->name('verification.notice');

    // Verify email via link
    Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    // Resend email verification notification
    Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('verification.send');

    /*
    |----------------------------------------------------------------------
    | Password Confirmation Routes
    |----------------------------------------------------------------------
    */
    Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])
        ->name('password.confirm');

    Route::post('confirm-password', [ConfirmablePasswordController::class, 'store'])
        ->name('password.confirm.store');

    /*
    |----------------------------------------------------------------------
    | Password Update Route
    |----------------------------------------------------------------------
    */
    Route::put('password', [PasswordController::class, 'update'])
        ->name('password.update');

    /*
    |----------------------------------------------------------------------
    | Logout Route
    |----------------------------------------------------------------------
    */
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');
});