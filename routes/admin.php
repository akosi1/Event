<?php

use App\Http\Controllers\Admin\BackupController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\EventController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\LoginLogsController;
use App\Http\Controllers\EventJoinController;

use Illuminate\Support\Facades\Route;

// Admin Protected Routes
Route::middleware(['admin'])->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    
    // Event join route (requires auth)
    Route::middleware('auth')->group(function () {
        Route::post('/events/{event}/join', [EventController::class, 'join']);
    });
    
    // Logout routes - support both GET and POST
    Route::get('/logout', [AuthController::class, 'logout'])->name('admin.logout.get');
    Route::post('/logout', [AuthController::class, 'logout'])->name('admin.logout');

    // Print summary route (these must come before resource routes)
    Route::get('/events/print/summary', [EventController::class, 'printSummary'])->name('admin.events.print-summary');
    Route::get('/events/print', [EventController::class, 'print'])->name('admin.events.print');
    Route::post('/events/update-print-settings', [EventController::class, 'updatePrintSettings'])->name('admin.events.update-print-settings');

    // Archive routes (MUST come before resource routes)
    Route::get('/events/archive', [EventController::class, 'archive'])->name('admin.events.archive');
    Route::post('/events/{id}/restore', [EventController::class, 'restore'])->name('admin.events.restore');
    Route::delete('/events/{id}/force-delete', [EventController::class, 'forceDelete'])->name('admin.events.forceDelete');

    // Events resource routes
    Route::resource('/events', EventController::class)->names('admin.events');
    Route::get('/events/token/{token}', [EventController::class, 'showByToken']);
    Route::post('/events/{event}/regenerate-token', [EventController::class, 'regenerateToken'])->name('admin.events.regenerateToken');
    Route::post('/events/{event}/feedback', [EventController::class, 'storeFeedback'])->name('admin.events.storeFeedback');
    
    // Event Joins Admin
    Route::middleware('can:approve-eventjoin')->group(function () {
        Route::get('/event-joins', [EventJoinController::class, 'index'])->name('admin.event-joins.index');
        Route::post('/event-joins/{eventJoin}/approve', [EventJoinController::class, 'approve'])->name('admin.event-joins.approve');
        Route::post('/event-joins/{eventJoin}/reject', [EventJoinController::class, 'reject'])->name('admin.event-joins.reject');
        Route::get('/event-joins/print', [EventJoinController::class, 'print'])->name('admin.event-joins.print');
        Route::post('/event-joins/update-print-settings', [EventJoinController::class, 'updatePrintSettings'])->name('admin.event-joins.update-print-settings');
    });

    // Login Logs Management
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/login-logs', [LoginLogsController::class, 'index'])->name('login-logs.index');
        Route::get('/login-logs/data', [LoginLogsController::class, 'getData'])->name('login-logs.data');
        Route::get('/login-logs/{loginLog}', [LoginLogsController::class, 'show'])->name('login-logs.show');
        Route::post('/login-logs/cleanup', [LoginLogsController::class, 'cleanup'])->name('login-logs.cleanup');
    });
   
    // Users
    Route::resource('/users', UserController::class)->names('admin.users');

    // Certificates
    Route::get('/certificates', [AdminController::class, 'certificates'])->name('admin.certificates');
    Route::get('/certificates/{certificate}/download', [AdminController::class, 'download'])->name('admin.certificates.download');

    // Backup
    Route::post('/admin/backup/download', [BackupController::class, 'download'])->name('admin.backup.download');
     Route::get('events/{event}/document/download', [App\Http\Controllers\Admin\EventController::class, 'downloadDocument'])
        ->name('events.document.download');
    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index'])->name('admin.notifications.index');
    Route::get('/notifications/count', [NotificationController::class, 'getUnreadCount'])->name('admin.notifications.count');
    Route::patch('/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->name('admin.notifications.read');
    Route::patch('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('admin.notifications.read-all');
    Route::delete('/notifications/{notification}', [NotificationController::class, 'destroy'])->name('admin.notifications.destroy');
});