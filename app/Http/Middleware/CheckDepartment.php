<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DepartmentMiddleware
{
    public function handle(Request $request, Closure $next, string $department): Response
    {
        $user = $request->user();

        if (!$user || $user->department !== $department) {
            abort(403, 'Unauthorized: You do not belong to the required department.');
        }

        return $next($request);
    }
}
