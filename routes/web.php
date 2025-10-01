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

/
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


Route::get('ms365-verify', [MS365OTPController::class, 'showMS365Form'])->name('ms365.verify');
Route::post('ms365-verify', [MS365OTPController::class, 'verifyMS365Account']);

Route::get('otp-verify', [MS365OTPController::class, 'showOTPForm'])->name('otp.verify.form');
Route::post('otp-verify', [MS365OTPController::class, 'verifyOTP']);
Route::post('resend-otp', [MS365OTPController::class, 'resendOTP']);


Route::get('/send-email', [EmailController::class, 'sendEmail'])->name('email.send');


Route::get('/debug-mail', function () {
    $config = Config::get('mail.mailers.smtp');

    try {
        Mail::raw('Debug email from web route', function ($m) {
            $m->to(env('MAIL_DEBUG_RECEIVER', 'you@example.com'))
              ->subject('Debug Mail Test');
        });
        $status = 'Mail sent successfully!';
    } catch (\Exception $e) {
        $status = 'Mail failed: ' . $e->getMessage();
        Log::error('Mail debug error: ' . $e->getMessage());
    }

    return response()->json([
        'status' => $status,
        'mailer' => config('mail.default'),
        'smtp_config' => $config,
        'from' => config('mail.from'),
        'env_mail_host' => env('MAIL_HOST'),
        'env_mail_username' => env('MAIL_USERNAME'),
    ]);
});


Route::get('/logs', function () {
    $logFile = storage_path('logs/laravel.log');
    if (!File::exists($logFile)) {
        return response('<pre>No log file found.</pre>');
    }
    $logs = File::get($logFile);
    return response("<pre>{$logs}</pre>");
});
Route::get('/mail-log', function () {
    $logFile = storage_path('logs/laravel.log');

    if (!File::exists($logFile)) {
        return response('<pre>No log file found.</pre>');
    }

    $logs = File::get($logFile);

    return response("<pre>{$logs}</pre>");
});


require __DIR__.'/auth.php';
