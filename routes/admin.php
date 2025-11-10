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

    // Print summary route - MUST be before resource routes
    Route::get('/events/print/summary', [EventController::class, 'printSummary'])->name('admin.events.print-summary');

    // Events resource routes
    Route::resource('/events', EventController::class)->names('admin.events');
    //Events Admin
    Route::middleware('auth', 'can:approve-eventjoin')->name('admin.')->group(function () {
    Route::get('event-joins', [EventJoinController::class, 'index'])->name('event-joins.index');
    Route::post('event-joins/{eventJoin}/approve', [EventJoinController::class, 'approve'])->name('event-joins.approve');
    Route::post('event-joins/{eventJoin}/reject', [EventJoinController::class, 'reject'])->name('event-joins.reject');
});
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('event-joins/print', [EventJoinController::class, 'print'])
        ->name('event-joins.print');
});

Route::prefix('admin')->name('admin.')->group(function () {
    Route::post('event-joins/update-print-settings', [EventJoinController::class, 'updatePrintSettings'])
        ->name('event-joins.update-print-settings');

         Route::get('/events/print', [EventController::class, 'print'])->name('events.print');
         Route::post('/events/update-print-settings', [EventController::class, 'updatePrintSettings'])
        ->name('events.update-print-settings');
});

    // Users resource routes
    Route::resource('/users', UserController::class)->names('admin.users');

    Route::get('/certificates', [AdminController::class, 'certificates'])->name('admin.certificates');
    Route::get('admin/certificates/{certificate}/download', [AdminController::class, 'download'])
    ->name('admin.certificates.download');


    Route::get('/events/token/{token}', [EventController::class, 'showByToken']);
   Route::post('/admin/events/{event}/regenerate-token', [EventController::class, 'regenerateToken']);
   
    // Notification routes
    Route::get('/notifications', [NotificationController::class, 'index'])->name('admin.notifications.index');
    Route::get('/notifications/count', [NotificationController::class, 'getUnreadCount'])->name('admin.notifications.count');
    Route::patch('/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->name('admin.notifications.read');
    Route::patch('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('admin.notifications.read-all');
    Route::delete('/notifications/{notification}', [NotificationController::class, 'destroy'])->name('admin.notifications.destroy');
});
