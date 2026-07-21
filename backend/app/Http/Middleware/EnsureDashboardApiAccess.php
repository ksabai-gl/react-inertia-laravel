<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class EnsureDashboardApiAccess
{
    /**
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! config('services.dashboard.require_auth', false)) {
            return $next($request);
        }

        if ($request->user() !== null) {
            return $next($request);
        }

        return response()->json([
            'success' => false,
            'error' => [
                'code' => 'UNAUTHORIZED',
                'message' => 'Authentication is required to access this resource.',
                'trace_id' => (string) Str::uuid(),
            ],
        ], 401);
    }
}
