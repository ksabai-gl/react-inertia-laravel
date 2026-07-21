<?php

namespace App\Services;

use UnexpectedValueException;

class DashboardService
{
    private const ALLOWED_ACTIVITY_STATUSES = ['active', 'paused', 'failed'];

    /**
     * @param array{page?: int, per_page?: int, status?: string, region?: string} $filters
     * @return array{
     *     stats: list<array{key: string, label: string, value: string, hint: string}>,
     *     activity: list<array{name: string, phone: string, module: string, status: string, region: string, updated: string}>,
     *     breakdown: list<array{label: string, count: int, percent: int, color: string}>,
     *     regions: list<array{region: string, records: int}>,
     *     activity_meta?: array{current_page: int, per_page: int, last_page: int, total: int, from: int|null, to: int|null}
     * }
     */
    public function summary(array $filters = []): array
    {
        $payload = [
            'stats' => $this->buildStats(),
            'activity' => $this->buildActivity(),
            'breakdown' => $this->buildBreakdown(),
            'regions' => $this->buildRegions(),
        ];

        $this->assertRequiredSections($payload);

        $filteredActivity = $this->applyActivityFilters($payload['activity'], $filters);
        $pagination = $this->resolvePagination($filters, count($filteredActivity));

        if ($pagination !== null) {
            $payload['activity'] = array_values(array_slice($filteredActivity, $pagination['offset'], $pagination['per_page']));
            $payload['activity_meta'] = [
                'current_page' => $pagination['current_page'],
                'per_page' => $pagination['per_page'],
                'last_page' => $pagination['last_page'],
                'total' => $pagination['total'],
                'from' => $pagination['from'],
                'to' => $pagination['to'],
            ];
        } else {
            $payload['activity'] = $filteredActivity;
        }

        return $payload;
    }

    /**
     * @param array<string, mixed> $payload
     */
    private function assertRequiredSections(array $payload): void
    {
        foreach (['stats', 'activity', 'breakdown', 'regions'] as $requiredSection) {
            if (! array_key_exists($requiredSection, $payload)) {
                throw new UnexpectedValueException("Missing required dashboard section: {$requiredSection}");
            }
        }

        foreach ($payload['activity'] as $item) {
            if (! in_array($item['status'], self::ALLOWED_ACTIVITY_STATUSES, true)) {
                throw new UnexpectedValueException(sprintf(
                    'Invalid dashboard activity status "%s" for "%s"',
                    $item['status'],
                    $item['name'],
                ));
            }
        }
    }

    /**
     * @param list<array{name: string, phone: string, module: string, status: string, region: string, updated: string}> $activity
     * @param array{status?: string, region?: string} $filters
     * @return list<array{name: string, phone: string, module: string, status: string, region: string, updated: string}>
     */
    private function applyActivityFilters(array $activity, array $filters): array
    {
        $status = isset($filters['status']) ? (string) $filters['status'] : null;
        $region = isset($filters['region']) ? strtoupper((string) $filters['region']) : null;

        return array_values(array_filter($activity, function (array $item) use ($status, $region): bool {
            if ($status !== null && $item['status'] !== $status) {
                return false;
            }

            if ($region !== null && $item['region'] !== $region) {
                return false;
            }

            return true;
        }));
    }

    /**
     * @param array{page?: int, per_page?: int} $filters
     * @return array{offset: int, current_page: int, per_page: int, last_page: int, total: int, from: int|null, to: int|null}|null
     */
    private function resolvePagination(array $filters, int $total): ?array
    {
        if (! array_key_exists('page', $filters) && ! array_key_exists('per_page', $filters)) {
            return null;
        }

        $defaultPerPage = max(1, (int) config('services.dashboard.pagination_default_per_page', 10));
        $maxPerPage = max(1, (int) config('services.dashboard.pagination_max_per_page', 50));

        $requestedPerPage = isset($filters['per_page']) ? (int) $filters['per_page'] : $defaultPerPage;
        $perPage = min(max($requestedPerPage, 1), $maxPerPage);

        $requestedPage = isset($filters['page']) ? (int) $filters['page'] : 1;
        $lastPage = max(1, (int) ceil($total / $perPage));
        $currentPage = min(max($requestedPage, 1), $lastPage);
        $offset = ($currentPage - 1) * $perPage;

        $from = $total === 0 ? null : $offset + 1;
        $to = $total === 0 ? null : min($offset + $perPage, $total);

        return [
            'offset' => $offset,
            'current_page' => $currentPage,
            'per_page' => $perPage,
            'last_page' => $lastPage,
            'total' => $total,
            'from' => $from,
            'to' => $to,
        ];
    }

    /**
     * @return list<array{key: string, label: string, value: string, hint: string}>
     */
    private function buildStats(): array
    {
        return [
            [
                'key' => 'records',
                'label' => 'Total Test Records',
                'value' => '23',
                'hint' => '17 active',
            ],
            [
                'key' => 'phones',
                'label' => 'Phone Numbers',
                'value' => '5',
                'hint' => 'Monitored globally',
            ],
            [
                'key' => 'alerts',
                'label' => 'Open Alerts',
                'value' => '3',
                'hint' => 'Require attention',
            ],
            [
                'key' => 'countries',
                'label' => 'Countries',
                'value' => '11',
                'hint' => 'Coverage regions',
            ],
        ];
    }

    /**
     * @return list<array{name: string, phone: string, module: string, status: string, region: string, updated: string}>
     */
    private function buildActivity(): array
    {
        return [
            [
                'name' => 'US Toll-Free Regression Suite',
                'phone' => '+1 (800) 555-0101',
                'module' => 'Regression Tests',
                'status' => 'active',
                'region' => 'US',
                'updated' => '17 Jun, 13:10',
            ],
            [
                'name' => 'UK Retail IVR Discovery',
                'phone' => '+44 20 7946 0958',
                'module' => 'Discovery Scans',
                'status' => 'active',
                'region' => 'GB',
                'updated' => '17 Jun, 12:45',
            ],
            [
                'name' => 'APAC Banking Path Map',
                'phone' => '+65 6123 4567',
                'module' => 'Discovery Scans',
                'status' => 'paused',
                'region' => 'SG',
                'updated' => '17 Jun, 11:20',
            ],
            [
                'name' => 'DE Support Line Regression',
                'phone' => '+49 30 123456',
                'module' => 'Regression Tests',
                'status' => 'failed',
                'region' => 'DE',
                'updated' => '16 Jun, 18:05',
            ],
            [
                'name' => 'India Prepaid Menu Audit',
                'phone' => '+91 22 4000 1234',
                'module' => 'Regression Tests',
                'status' => 'active',
                'region' => 'IN',
                'updated' => '16 Jun, 16:40',
            ],
            [
                'name' => 'Brazil Collections Discovery',
                'phone' => '+55 11 4002 8922',
                'module' => 'Discovery Scans',
                'status' => 'paused',
                'region' => 'BR',
                'updated' => '16 Jun, 14:15',
            ],
            [
                'name' => 'CA Enterprise IVR Suite',
                'phone' => '+1 (416) 555-0199',
                'module' => 'Regression Tests',
                'status' => 'active',
                'region' => 'CA',
                'updated' => '16 Jun, 09:30',
            ],
            [
                'name' => 'FR Hotel Booking Paths',
                'phone' => '+33 1 42 68 53 00',
                'module' => 'Discovery Scans',
                'status' => 'active',
                'region' => 'FR',
                'updated' => '15 Jun, 21:10',
            ],
            [
                'name' => 'AU Telco Regression Pack',
                'phone' => '+61 2 9876 5432',
                'module' => 'Regression Tests',
                'status' => 'failed',
                'region' => 'AU',
                'updated' => '15 Jun, 17:55',
            ],
            [
                'name' => 'JP Customer Care Scan',
                'phone' => '+81 3 1234 5678',
                'module' => 'Discovery Scans',
                'status' => 'active',
                'region' => 'JP',
                'updated' => '15 Jun, 08:20',
            ],
        ];
    }

    /**
     * @return list<array{label: string, count: int, percent: int, color: string}>
     */
    private function buildBreakdown(): array
    {
        return [
            ['label' => 'Active', 'count' => 17, 'percent' => 74, 'color' => '#10b981'],
            ['label' => 'Paused', 'count' => 3, 'percent' => 13, 'color' => '#fb923c'],
            ['label' => 'Failed', 'count' => 2, 'percent' => 9, 'color' => '#ef4444'],
            ['label' => 'Other', 'count' => 1, 'percent' => 4, 'color' => '#d4d4d8'],
        ];
    }

    /**
     * @return list<array{region: string, records: int}>
     */
    private function buildRegions(): array
    {
        return [
            ['region' => 'US', 'records' => 6],
            ['region' => 'GB', 'records' => 4],
            ['region' => 'DE', 'records' => 3],
            ['region' => 'IN', 'records' => 3],
            ['region' => 'FR', 'records' => 2],
        ];
    }
}
