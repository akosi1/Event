<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ContentSecurityPolicy
        {
            /**
             * Handle an incoming request.
             *
             * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
             */
            public function handle($request, Closure $next)
        {
            $response = $next($request);

            $response->headers->set('Content-Security-Policy', 
                "default-src 'self'; 
                script-src 'self' cdn.jsdelivr.net; 
                style-src 'self' cdn.jsdelivr.net; 
                img-src 'self' data:; 
                object-src 'none'; 
                base-uri 'self'; 
                form-action 'self'; 
                frame-ancestors 'none'");

            return $response;
        }
}
