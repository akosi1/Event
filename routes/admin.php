<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\EventController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\EventJoinController;

use Illuminate\Support\Facades\Route;

// Admin Authentication Routes
// Route::get('/', [AuthController::class, 'showLoginForm'])->name('admin.login');
// Route::post('/login', [AuthController::class, 'login'])->name('admin.login.post');

// Admin Protected Routes
Route::middleware(['admin'])->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');

    // Logout routes - support both GET and POST
    Route::get('/logout', [AuthController::class, 'logout'])->name('admin.logout.get');
    Route::post('/logout', [AuthController::class, 'logout'])->name('admin.logout');

    // Events Management Routes - Custom routes BEFORE resource routes
    Route::prefix('events')->name('admin.events.')->group(function () {
        // Print Routes - MUST BE FIRST (before {event} parameter routes)
        Route::get('/print', [EventController::class, 'print'])->name('print');
        Route::post('/update-print-settings', [EventController::class, 'updatePrintSettings'])->name('update-print-settings');
        
        // Standard CRUD routes
        Route::get('/', [EventController::class, 'index'])->name('index');
        Route::get('/create', [EventController::class, 'create'])->name('create');
        Route::post('/', [EventController::class, 'store'])->name('store');
        
        // Routes with {event} parameter - MUST BE LAST
        Route::get('/{event}', [EventController::class, 'show'])->name('show');
        Route::get('/{event}/edit', [EventController::class, 'edit'])->name('edit');
        Route::put('/{event}', [EventController::class, 'update'])->name('update');
        Route::patch('/{event}', [EventController::class, 'update'])->name('update');
        Route::delete('/{event}', [EventController::class, 'destroy'])->name('destroy');
        Route::post('/{event}/feedback', [EventController::class, 'storeFeedback'])->name('feedback.store');
    });

    // Event Joins Routes - with authorization middleware
    Route::middleware(['auth', 'can:approve-eventjoin'])->prefix('event-joins')->name('admin.event-joins.')->group(function () {
        // Print Routes - MUST BE FIRST
        Route::get('/print', [EventJoinController::class, 'print'])->name('print');
        Route::post('/update-print-settings', [EventJoinController::class, 'updatePrintSettings'])->name('update-print-settings');
        
        // Standard routes
        Route::get('/', [EventJoinController::class, 'index'])->name('index');
        
        // Routes with {eventJoin} parameter - MUST BE LAST
        Route::post('/{eventJoin}/approve', [EventJoinController::class, 'approve'])->name('approve');
        Route::post('/{eventJoin}/reject', [EventJoinController::class, 'reject'])->name('reject');
    });

    // Users resource routes
    Route::resource('/users', UserController::class)->names('admin.users');

    // Certificates routes
    Route::get('/certificates', [AdminController::class, 'certificates'])->name('admin.certificates');
    Route::get('/certificates/{certificate}/download', [AdminController::class, 'download'])->name('admin.certificates.download');

    // Notification routes
    Route::prefix('notifications')->name('admin.notifications.')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('index');
        Route::get('/count', [NotificationController::class, 'getUnreadCount'])->name('count');
        Route::patch('/{notification}/read', [NotificationController::class, 'markAsRead'])->name('read');
        Route::patch('/read-all', [NotificationController::class, 'markAllAsRead'])->name('read-all');
        Route::delete('/{notification}', [NotificationController::class, 'destroy'])->name('destroy');
    });
});