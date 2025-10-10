<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // === SECURITY HEADERS ===

        // Enforce HTTPS across the site
        $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains; preload');

        // Prevent MIME-type sniffing
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        // Prevent clickjacking
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');

        // Enable basic XSS protection
        $response->headers->set('X-XSS-Protection', '1; mode=block');

        // Limit referrer info
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        // Restrict access to browser features
        $response->headers->set('Permissions-Policy', 'geolocation=(), microphone=(), camera=(), payment=(), usb=(), vr=(), fullscreen=(), magnetometer=(), accelerometer=()');

        // === SAFE CONTENT SECURITY POLICY (CSP) ===
        $response->headers->set(
            'Content-Security-Policy',
            "default-src 'self'; 
             script-src 'self'; 
             style-src 'self'; 
             object-src 'none'; 
             frame-ancestors 'none'; 
             base-uri 'self'; 
             form-action 'self'"
        );

        return $response;
    }
}
