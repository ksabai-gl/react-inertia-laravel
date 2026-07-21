<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\DashboardQueryRequest;
use App\Services\DashboardService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;
use Throwable;
use UnexpectedValueException;

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
    public function __invoke(DashboardQueryRequest $request): JsonResponse
    {
        try {
            $summary = $this->dashboard->summary($request->validated());

            return response()->json([
                'success' => true,
                'data' => $summary,
                'meta' => [
                    'generated_at' => now()->toIso8601String(),
                    'source' => 'php-api',
                ],
            ]);
        } catch (Throwable $exception) {
            $errorCode = 'DASHBOARD_UNAVAILABLE';
            $errorMessage = 'Dashboard data is currently unavailable.';

            if ($exception instanceof UnexpectedValueException) {
                $errorCode = 'DASHBOARD_CONTRACT_ERROR';
                $errorMessage = 'Dashboard data contract validation failed.';
            }

            return response()->json([
                'success' => false,
                'error' => [
                    'code' => $errorCode,
                    'message' => $errorMessage,
                    'trace_id' => (string) Str::uuid(),
                ],
            ], 500);
        }
    }
}
