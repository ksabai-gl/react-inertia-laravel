<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

class DashboardAccess
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $mode = strtolower((string) config('dashboard.access_mode', 'disabled'));

        if ($mode === 'disabled') {
            return $next($request);
        }

        if ($request->is('api/*') || $request->expectsJson()) {
            return $this->handleApiRequest($request, $next);
        }

        return $this->handleWebRequest($request, $next);
    }

    /**
     * @param  Closure(Request): Response  $next
     */
    private function handleApiRequest(Request $request, Closure $next): Response
    {
        $expectedToken = (string) config('dashboard.api_token', '');
        $receivedToken = (string) $request->bearerToken();

        if ($expectedToken === '' || $receivedToken === '' || ! hash_equals($expectedToken, $receivedToken)) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'DASHBOARD_UNAUTHORIZED',
                    'message' => 'Dashboard access is not authorized.',
                ],
            ], 401);
        }

        return $next($request);
    }

    /**
     * @param  Closure(Request): Response  $next
     */
    private function handleWebRequest(Request $request, Closure $next): Response
    {
        $configuredUser = (string) config('dashboard.basic_user', '');
        $configuredPasswordHash = (string) config('dashboard.basic_pass_hash', '');

        $username = (string) $request->getUser();
        $password = (string) $request->getPassword();

        if ($configuredUser === '' || $configuredPasswordHash === '') {
            return $this->unauthorizedChallenge();
        }

        $isValidUser = hash_equals($configuredUser, $username);
        $isValidPassword = $password !== '' && Hash::check($password, $configuredPasswordHash);

        if (! $isValidUser || ! $isValidPassword) {
            return $this->unauthorizedChallenge();
        }

        return $next($request);
    }

    private function unauthorizedChallenge(): Response
    {
        return response('Unauthorized', 401, [
            'WWW-Authenticate' => 'Basic realm="Dashboard"',
        ]);
    }
}
