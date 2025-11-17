<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class IpGeolocation
{
    /**
     * Get the real IP address even behind proxies
     */
    public static function getRealIp(): string
    {
        $headers = [
            'HTTP_CF_CONNECTING_IP', // Cloudflare
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_FORWARDED',
            'HTTP_X_CLUSTER_CLIENT_IP',
            'HTTP_FORWARDED_FOR',
            'HTTP_FORWARDED',
            'HTTP_CLIENT_IP',
            'REMOTE_ADDR'
        ];

        foreach ($headers as $header) {
            if (!empty($_SERVER[$header])) {
                $ip = $_SERVER[$header];
                // Handle comma-separated IPs (proxy chains)
                if (str_contains($ip, ',')) {
                    $ip = trim(explode(',', $ip)[0]);
                }
                
                // Validate IP
                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                    return $ip;
                }
            }
        }

        // Fallback to REMOTE_ADDR or default
        return $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
    }

    /**
     * Get geolocation data for an IP address with fallback for localhost
     */
    public static function getGeolocation(string $ip): array
    {
        // Default data structure
        $defaultData = [
            'ip' => $ip,
            'city' => null,
            'region' => null,
            'country' => null,
            'country_code' => null,
            'latitude' => null,
            'longitude' => null,
            'timezone' => null,
            'isp' => null,
        ];

        // Handle localhost/private IPs - use public IP geolocation
        if (self::isPrivateIp($ip)) {
            // Get public IP and geolocate it for localhost testing
            $publicIp = self::getPublicIp();
            if ($publicIp && $publicIp !== $ip) {
                return self::fetchGeolocationData($publicIp);
            }
            
            // Return default localhost data
            return array_merge($defaultData, [
                'city' => 'Localhost',
                'region' => 'Local Network',
                'country' => 'Local',
                'country_code' => 'LC',
                'latitude' => 0.0,
                'longitude' => 0.0,
                'timezone' => config('app.timezone'),
                'isp' => 'Local Network',
            ]);
        }

        // Fetch geolocation for public IPs
        return self::fetchGeolocationData($ip);
    }

    /**
     * Check if IP is private/localhost
     */
    private static function isPrivateIp(string $ip): bool
    {
        if ($ip === '127.0.0.1' || $ip === '::1') {
            return true;
        }

        return !filter_var(
            $ip,
            FILTER_VALIDATE_IP,
            FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE
        );
    }

    /**
     * Get the server's public IP address
     */
    private static function getPublicIp(): ?string
    {
        try {
            $response = Http::timeout(3)->get('https://api.ipify.org?format=json');
            if ($response->successful()) {
                return $response->json()['ip'] ?? null;
            }
        } catch (\Exception $e) {
            Log::warning('Failed to get public IP: ' . $e->getMessage());
        }
        return null;
    }

    /**
     * Fetch geolocation data from API with caching
     */
    private static function fetchGeolocationData(string $ip): array
    {
        $cacheKey = "geolocation:{$ip}";
        
        // Try cache first (24 hour cache)
        $cached = Cache::get($cacheKey);
        if ($cached) {
            return $cached;
        }

        $defaultData = [
            'ip' => $ip,
            'city' => null,
            'region' => null,
            'country' => null,
            'country_code' => null,
            'latitude' => null,
            'longitude' => null,
            'timezone' => null,
            'isp' => null,
        ];

        try {
            // Try ip-api.com (free, 45 requests/minute)
            $response = Http::timeout(5)->get("http://ip-api.com/json/{$ip}?fields=status,country,countryCode,region,regionName,city,lat,lon,timezone,isp");
            
            if ($response->successful()) {
                $data = $response->json();
                
                if (($data['status'] ?? '') === 'success') {
                    $geolocationData = [
                        'ip' => $ip,
                        'city' => $data['city'] ?? null,
                        'region' => $data['regionName'] ?? null,
                        'country' => $data['country'] ?? null,
                        'country_code' => $data['countryCode'] ?? null,
                        'latitude' => $data['lat'] ?? null,
                        'longitude' => $data['lon'] ?? null,
                        'timezone' => $data['timezone'] ?? null,
                        'isp' => $data['isp'] ?? null,
                    ];
                    
                    // Cache for 24 hours
                    Cache::put($cacheKey, $geolocationData, now()->addHours(24));
                    return $geolocationData;
                }
            }

            // Fallback to ipapi.co (free, 1000 requests/day)
            $response = Http::timeout(5)->get("https://ipapi.co/{$ip}/json/");
            
            if ($response->successful()) {
                $data = $response->json();
                
                if (!isset($data['error'])) {
                    $geolocationData = [
                        'ip' => $ip,
                        'city' => $data['city'] ?? null,
                        'region' => $data['region'] ?? null,
                        'country' => $data['country_name'] ?? null,
                        'country_code' => $data['country_code'] ?? null,
                        'latitude' => $data['latitude'] ?? null,
                        'longitude' => $data['longitude'] ?? null,
                        'timezone' => $data['timezone'] ?? null,
                        'isp' => $data['org'] ?? null,
                    ];
                    
                    Cache::put($cacheKey, $geolocationData, now()->addHours(24));
                    return $geolocationData;
                }
            }

        } catch (\Exception $e) {
            Log::error('Geolocation API error: ' . $e->getMessage());
        }

        // Return default data if all APIs fail
        return $defaultData;
    }

    /**
     * Get user agent string
     */
    public static function getUserAgent(): ?string
    {
        return $_SERVER['HTTP_USER_AGENT'] ?? null;
    }

    /**
     * Parse device information from user agent
     */
    public static function parseDeviceInfo(?string $userAgent): array
    {
        if (!$userAgent) {
            return [
                'device_type' => 'Unknown',
                'browser' => 'Unknown',
                'os' => 'Unknown',
            ];
        }

        // Detect device type
        $deviceType = 'Desktop';
        if (preg_match('/mobile|android|iphone|ipad|ipod/i', $userAgent)) {
            $deviceType = 'Mobile';
        } elseif (preg_match('/tablet|ipad/i', $userAgent)) {
            $deviceType = 'Tablet';
        }

        // Detect browser
        $browser = 'Unknown';
        if (preg_match('/edge|edg/i', $userAgent)) {
            $browser = 'Edge';
        } elseif (preg_match('/chrome/i', $userAgent)) {
            $browser = 'Chrome';
        } elseif (preg_match('/firefox/i', $userAgent)) {
            $browser = 'Firefox';
        } elseif (preg_match('/safari/i', $userAgent)) {
            $browser = 'Safari';
        } elseif (preg_match('/opera|opr/i', $userAgent)) {
            $browser = 'Opera';
        }

        // Detect OS
        $os = 'Unknown';
        if (preg_match('/windows nt 10/i', $userAgent)) {
            $os = 'Windows 10';
        } elseif (preg_match('/windows nt 11/i', $userAgent)) {
            $os = 'Windows 11';
        } elseif (preg_match('/windows/i', $userAgent)) {
            $os = 'Windows';
        } elseif (preg_match('/mac os x/i', $userAgent)) {
            $os = 'macOS';
        } elseif (preg_match('/linux/i', $userAgent)) {
            $os = 'Linux';
        } elseif (preg_match('/android/i', $userAgent)) {
            $os = 'Android';
        } elseif (preg_match('/iphone|ipad|ipod/i', $userAgent)) {
            $os = 'iOS';
        }

        return [
            'device_type' => $deviceType,
            'browser' => $browser,
            'os' => $os,
        ];
    }
}