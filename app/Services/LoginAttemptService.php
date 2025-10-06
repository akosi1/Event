<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class LoginAttemptService
{
    private const MAX_ATTEMPTS = 3;
    private const LOCKOUT_DURATION = 1; // minutes

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
     * Increment login attempts
     */
    public function increment(string $identifier): int
    {
        $key = $this->getCacheKey($identifier);
        $attempts = Cache::get($key, 0) + 1;
        
        // Store attempts for 15 minutes
        Cache::put($key, $attempts, now()->addMinutes(self::LOCKOUT_DURATION));
        
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
            return max(0, now()->diffInSeconds($endTime, false));
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
     * Lock out the user
     */
    private function lockout(string $identifier): void
    {
        $lockoutKey = $this->getLockoutKey($identifier);
        $endTime = now()->addMinutes(self::LOCKOUT_DURATION);
        
        Cache::put($lockoutKey, $endTime, $endTime);
    }

    /**
     * Clear login attempts
     */
    public function clear(string $identifier): void
    {
        Cache::forget($this->getCacheKey($identifier));
        Cache::forget($this->getLockoutKey($identifier));
    }

    /**
     * Get max attempts allowed
     */
    public function getMaxAttempts(): int
    {
        return self::MAX_ATTEMPTS;
    }

    /**
     * Get lockout duration in minutes
     */
    public function getLockoutDuration(): int
    {
        return self::LOCKOUT_DURATION;
    }
}