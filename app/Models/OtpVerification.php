<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class OtpVerification extends Model
{
    use HasFactory;

    protected $fillable = [
        'email',
        'otp_code',
        'type',
        'ip_address',
        'expires_at',
        'used_at',
        'attempts'
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'used_at' => 'datetime',
    ];

    /**
     * Generate a new OTP for the given email
     */
    public static function generateOtp(string $email, string $type = 'login', ?string $ipAddress = null): self
    {
        // Clean up old/expired OTPs for this email and type
        self::where('email', $email)
            ->where('type', $type)
            ->where(function ($query) {
                $query->where('expires_at', '<', now())
                      ->orWhereNotNull('used_at');
            })
            ->delete();

        // Generate 6-digit OTP
        $otpCode = str_pad(random_int(100000, 999999), 6, '0', STR_PAD_LEFT);

        // Create new OTP record
        return self::create([
            'email' => strtolower(trim($email)),
            'otp_code' => $otpCode,
            'type' => $type,
            'ip_address' => $ipAddress,
            'expires_at' => now()->addMinutes(10), // 10 minutes expiry
            'attempts' => 0
        ]);
    }

    /**
     * Verify OTP code
     */
    public static function verifyOtp(string $email, string $otpCode, string $type = 'login'): bool
    {
        $otp = self::where('email', strtolower(trim($email)))
            ->where('otp_code', $otpCode)
            ->where('type', $type)
            ->where('expires_at', '>', now())
            ->whereNull('used_at')
            ->first();

        if (!$otp) {
            return false;
        }

        // Mark as used
        $otp->update(['used_at' => now()]);
        
        return true;
    }

    /**
     * Increment failed attempts
     */
    public function incrementAttempts(): void
    {
        $this->increment('attempts');
    }

    /**
     * Check if OTP is expired
     */
    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    /**
     * Check if OTP is used
     */
    public function isUsed(): bool
    {
        return !is_null($this->used_at);
    }

    /**
     * Clean up expired OTPs (can be called by a scheduled job)
     */
    public static function cleanupExpired(): int
    {
        return self::where('expires_at', '<', now()->subHours(24))->delete();
    }
}