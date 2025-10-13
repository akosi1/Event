<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class LoginAttemptService
{
    private const MAX_ATTEMPTS = 3;
    private const BASE_LOCKOUT_MINUTES = 1; // 1 minute for first lockout

    /**
     * Get the cache key for login attempts
     */
    private function getCacheKey(string $identifier): string
    {
        return 'login_attempts_' . sha1($identifier);
    }

    /**
     * Get the cache key for lockout
     */
    private function getLockoutKey(string $identifier): string
    {
        return 'login_lockout_' . sha1($identifier);
    }

    /**
     * Get the cache key for total failed attempts (across all lockouts)
     */
    private function getTotalAttemptsKey(string $identifier): string
    {
        return 'total_failed_attempts_' . sha1($identifier);
    }

    /**
     * Increment login attempts
     */
    public function increment(string $identifier): int
    {
        $key = $this->getCacheKey($identifier);
        $attempts = Cache::get($key, 0) + 1;
        
        // Store attempts for 24 hours
        Cache::put($key, $attempts, now()->addDay());
        
        // If max attempts reached, lock the account
        if ($attempts >= self::MAX_ATTEMPTS) {
            $this->lockout($identifier);
        }
        
        return $attempts;
    }

    /**
     * Get current attempt count
     */
    public function getAttempts(string $identifier): int
    {
        return Cache::get($this->getCacheKey($identifier), 0);
    }

    /**
     * Get remaining attempts
     */
    public function getRemainingAttempts(string $identifier): int
    {
        $attempts = $this->getAttempts($identifier);
        return max(0, self::MAX_ATTEMPTS - $attempts);
    }

    /**
     * Check if user is locked out
     */
    public function isLockedOut(string $identifier): bool
    {
        return Cache::has($this->getLockoutKey($identifier));
    }

    /**
     * Get lockout end time
     */
    public function getLockoutEndTime(string $identifier): ?Carbon
    {
        $lockoutKey = $this->getLockoutKey($identifier);
        
        if (Cache::has($lockoutKey)) {
            return Cache::get($lockoutKey);
        }
        
        return null;
    }

    /**
     * Get remaining lockout time in seconds
     */
    public function getRemainingLockoutTime(string $identifier): int
    {
        $endTime = $this->getLockoutEndTime($identifier);
        
        if ($endTime) {
            $diff = now()->diffInSeconds($endTime, false);
            return max(0, $diff);
        }
        
        return 0;
    }
    
    /**
     * Get lockout end timestamp
     */
    public function getLockoutEndTimestamp(string $identifier): ?int
    {
        $endTime = $this->getLockoutEndTime($identifier);
        
        if ($endTime) {
            return $endTime->timestamp;
        }
        
        return null;
    }

    /**
     * Calculate progressive lockout duration based on total failed attempts
     * 3 attempts = 60 seconds (1 minute)
     * 6 attempts = 180 seconds (3 minutes)
     * 9 attempts = 540 seconds (9 minutes)
     * 12 attempts = 1620 seconds (27 minutes)
     */
    private function calculateLockoutDuration(int $totalAttempts): int
    {
        // Calculate which lockout cycle we're in (every 3 attempts)
        $lockoutCycle = ceil($totalAttempts / self::MAX_ATTEMPTS);
        
        // Progressive duration: multiply base duration by 3^(cycle-1)
        // Cycle 1 (3 attempts): 1 minute
        // Cycle 2 (6 attempts): 3 minutes  
        // Cycle 3 (9 attempts): 9 minutes
        // Cycle 4 (12 attempts): 27 minutes
        $minutes = self::BASE_LOCKOUT_MINUTES * pow(3, $lockoutCycle - 1);
        
        return $minutes;
    }

    /**
     * Lock out the user with progressive duration
     */
    private function lockout(string $identifier): void
    {
        // Increment total failed attempts
        $totalKey = $this->getTotalAttemptsKey($identifier);
        $totalAttempts = Cache::get($totalKey, 0) + self::MAX_ATTEMPTS;
        Cache::put($totalKey, $totalAttempts, now()->addDay());
        
        // Calculate progressive lockout duration
        $lockoutMinutes = $this->calculateLockoutDuration($totalAttempts);
        
        $lockoutKey = $this->getLockoutKey($identifier);
        $endTime = now()->addMinutes($lockoutMinutes);
        
        Cache::put($lockoutKey, $endTime, $endTime);
        
        // Reset current attempts counter
        Cache::forget($this->getCacheKey($identifier));
    }

    /**
     * Clear login attempts (on successful login)
     */
    public function clear(string $identifier): void
    {
        Cache::forget($this->getCacheKey($identifier));
        Cache::forget($this->getLockoutKey($identifier));
        Cache::forget($this->getTotalAttemptsKey($identifier));
    }

    /**
     * Get max attempts allowed
     */
    public function getMaxAttempts(): int
    {
        return self::MAX_ATTEMPTS;
    }

    /**
     * Get total failed attempts
     */
    public function getTotalFailedAttempts(string $identifier): int
    {
        return Cache::get($this->getTotalAttemptsKey($identifier), 0);
    }

    /**
     * Get lockout duration in minutes for display
     */
    public function getLockoutDurationMinutes(string $identifier): int
    {
        $totalAttempts = $this->getTotalFailedAttempts($identifier) ?: self::MAX_ATTEMPTS;
        return $this->calculateLockoutDuration($totalAttempts);
    }
}