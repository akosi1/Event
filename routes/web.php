<?php
    use Illuminate\Support\Facades\Route;
    use App\Http\Controllers\{ProfileController, EventJoinController, DashboardController};

    Route::get('/', fn() => view('welcome'));

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

    require __DIR__.'/auth.php';  

;