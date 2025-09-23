<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    ProfileController,
    EventJoinController,
    DashboardController
};

// Public route
Route::get('/', fn() => view('welcome'));

// Authentication routes (login, register, password reset, email verification, logout)
require __DIR__ . '/auth.php';

// Protected routes - only accessible by authenticated & verified users
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
