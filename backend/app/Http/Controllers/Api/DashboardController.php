<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\DashboardService;
use Illuminate\Http\JsonResponse;

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
        return response()->json([
            'success' => true,
            'data' => $this->dashboard->summary(),
            'meta' => [
                'generated_at' => now()->toIso8601String(),
                'source' => 'php-api',
            ],
        ]);
    }
}
