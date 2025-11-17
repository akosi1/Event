<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoginLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'email_attempted',
        'ip_address',
        'user_agent',
        'status',
        'city',
        'region',
        'country',
        'country_code',
        'latitude',
        'longitude',
        'timezone',
        'isp',
        'login_at',
        'logout_at',
        'session_duration',
        'logout_ip_address',
        'logout_city',
        'logout_region',
        'logout_country',
        'logout_country_code',
        'logout_latitude',
        'logout_longitude',
        'device_type',
        'browser',
        'os',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'login_at' => 'datetime',
        'logout_at' => 'datetime',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'logout_latitude' => 'decimal:8',
        'logout_longitude' => 'decimal:8',
        'session_duration' => 'integer',
    ];

    /**
     * Get the user associated with this login log.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get full location string for login.
     */
    public function getFullLocationAttribute(): ?string
    {
        $parts = array_filter([$this->city, $this->region, $this->country]);
        return !empty($parts) ? implode(', ', $parts) : null;
    }

    /**
     * Get full location string for logout.
     */
    public function getLogoutLocationAttribute(): ?string
    {
        if (!$this->logout_city && !$this->logout_country) {
            return null;
        }
        $parts = array_filter([$this->logout_city, $this->logout_region, $this->logout_country]);
        return !empty($parts) ? implode(', ', $parts) : null;
    }

    /**
     * Get status badge class for UI.
     */
    public function getStatusBadgeClassAttribute(): string
    {
        return match($this->status) {
            'success' => 'success',
            'failed' => 'warning',
            'locked_out' => 'danger',
            default => 'secondary',
        };
    }

    /**
     * Get status label.
     */
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'success' => 'Success',
            'failed' => 'Failed',
            'locked_out' => 'Locked Out',
            default => 'Unknown',
        };
    }

    /**
     * Get device info string.
     */
    public function getDeviceInfoAttribute(): string
    {
        $parts = array_filter([$this->device_type, $this->browser, $this->os]);
        return !empty($parts) ? implode(' • ', $parts) : 'Unknown Device';
    }

    /**
     * Get formatted session duration.
     */
    public function getFormattedDurationAttribute(): ?string
    {
        if (!$this->session_duration) {
            return null;
        }

        $hours = floor($this->session_duration / 3600);
        $minutes = floor(($this->session_duration % 3600) / 60);
        $seconds = $this->session_duration % 60;

        if ($hours > 0) {
            return sprintf('%dh %dm %ds', $hours, $minutes, $seconds);
        } elseif ($minutes > 0) {
            return sprintf('%dm %ds', $minutes, $seconds);
        } else {
            return sprintf('%ds', $seconds);
        }
    }

    /**
     * Check if session is still active (no logout).
     */
    public function isActive(): bool
    {
        return $this->status === 'success' && is_null($this->logout_at);
    }

    /**
     * Scope for successful logins only.
     */
    public function scopeSuccessful($query)
    {
        return $query->where('status', 'success');
    }

    /**
     * Scope for failed logins only.
     */
    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    /**
     * Scope for locked out attempts.
     */
    public function scopeLockedOut($query)
    {
        return $query->where('status', 'locked_out');
    }

    /**
     * Scope for recent logs.
     */
    public function scopeRecent($query, $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Scope for active sessions.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'success')->whereNull('logout_at');
    }

    /**
     * Get map marker data.
     */
    public function getMapMarkerDataAttribute(): array
    {
        return [
            'id' => $this->id,
            'email' => $this->email_attempted,
            'userName' => $this->user ? $this->user->full_name : 'Unknown User',
            'ip' => $this->ip_address,
            'status' => $this->status,
            'statusLabel' => $this->status_label,
            'city' => $this->city,
            'country' => $this->country,
            'location' => $this->full_location,
            'lat' => $this->latitude ? (float) $this->latitude : 0,
            'lng' => $this->longitude ? (float) $this->longitude : 0,
            'loginTime' => $this->login_at ? $this->login_at->format('Y-m-d H:i:s') : null,
            'logoutTime' => $this->logout_at ? $this->logout_at->format('Y-m-d H:i:s') : null,
            'duration' => $this->formatted_duration,
            'device' => $this->device_info,
            'logoutIp' => $this->logout_ip_address,
            'logoutLocation' => $this->logout_location,
            'logoutLat' => $this->logout_latitude ? (float) $this->logout_latitude : null,
            'logoutLng' => $this->logout_longitude ? (float) $this->logout_longitude : null,
            'date' => $this->created_at->format('Y-m-d H:i:s'),
            'dateHuman' => $this->created_at->diffForHumans(),
        ];
    }
}