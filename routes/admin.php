<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\EventController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\NotificationController;
use Illuminate\Support\Facades\Route;

// Admin Authentication Routes
Route::get('/', [AuthController::class, 'showLoginForm'])->name('admin.login');
Route::post('/login', [AuthController::class, 'login'])->name('admin.login.post');

// Admin Protected Routes
Route::middleware(['admin'])->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    
    // Logout routes - support both GET and POST
    Route::get('/logout', [AuthController::class, 'logout'])->name('admin.logout.get');
    Route::post('/logout', [AuthController::class, 'logout'])->name('admin.logout');
    
    Route::resource('/events', EventController::class)->names('admin.events');
    
    // Fixed print summary route - moved inside middleware group and fixed path
    Route::get('/events/print/summary', [EventController::class, 'printSummary'])->name('admin.events.print-summary');
    
    Route::resource('/users', UserController::class)->names('admin.users');
    Route::get('/certificates', [AdminController::class, 'certificates'])->name('admin.certificates');
    
    // Notification routes
    Route::get('/notifications', [NotificationController::class, 'index'])->name('admin.notifications.index');
    Route::get('/notifications/count', [NotificationController::class, 'getUnreadCount'])->name('admin.notifications.count');
    Route::patch('/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->name('admin.notifications.read');
    Route::patch('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('admin.notifications.read-all');
    Route::delete('/notifications/{notification}', [NotificationController::class, 'destroy'])->name('admin.notifications.destroy');
});
