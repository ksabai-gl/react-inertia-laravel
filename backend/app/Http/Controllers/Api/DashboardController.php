<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\DashboardService;
use Illuminate\Http\JsonResponse;
use Throwable;

class DashboardController extends Controller
{
    public function __construct(
        private readonly DashboardService $dashboard,
    ) {}

    /**
     * Demo JSON API: IVR dashboard summary.
     *
     * GET /api/dashboard
     */
    public function __invoke(): JsonResponse
    {
        try {
            return response()->json([
                'success' => true,
                'data' => $this->dashboard->summary(),
                'meta' => [
                    'generated_at' => now()->toIso8601String(),
                    'source' => 'php-api',
                ],
            ]);
        } catch (Throwable $exception) {
            report($exception);

            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'DASHBOARD_SUMMARY_UNAVAILABLE',
                    'message' => 'Unable to load dashboard summary.',
                ],
            ], 500);
        }
    }
}
