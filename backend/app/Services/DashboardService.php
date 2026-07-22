<?php

namespace App\Services;

use App\Repositories\DashboardRepository;

class DashboardService
{
    private const ALLOWED_STATUSES = ['active', 'paused', 'failed', 'other'];

    private const STATUS_COLORS = [
        'active' => '#10b981',
        'paused' => '#fb923c',
        'failed' => '#ef4444',
        'other' => '#d4d4d8',
    ];

    public function __construct(
        private readonly DashboardRepository $repository,
    ) {}

    /**
     * @return array{
     *     stats: list<array{key: string, label: string, value: string, hint: string}>,
     *     activity: list<array{record_key: string, name: string, phone: string, module: string, status: string, region: string, updated: string}>,
     *     breakdown: list<array{label: string, count: int, percent: int, color: string}>,
     *     regions: list<array{region: string, records: int}>
     * }
     */
    public function summary(): array
    {
        $activity = array_map($this->normalizeActivity(...), $this->repository->activity());
        $statusCounts = $this->repository->statusCounts();

        return [
            'stats' => [
                [
                    'key' => 'records',
                    'label' => 'Total Test Records',
                    'value' => (string) $this->repository->totalRecords(),
                    'hint' => $this->repository->activeRecords().' active',
                ],
                [
                    'key' => 'phones',
                    'label' => 'Phone Numbers',
                    'value' => (string) $this->repository->uniquePhoneCount(),
                    'hint' => 'Monitored globally',
                ],
                [
                    'key' => 'alerts',
                    'label' => 'Open Alerts',
                    'value' => (string) $this->repository->failedRecords(),
                    'hint' => 'Require attention',
                ],
                [
                    'key' => 'countries',
                    'label' => 'Countries',
                    'value' => (string) $this->repository->regionCount(),
                    'hint' => 'Coverage regions',
                ],
            ],
            'activity' => $activity,
            'breakdown' => $this->buildBreakdown($statusCounts),
            'regions' => array_map(static fn (array $item): array => [
                'region' => (string) $item['region'],
                'records' => max(0, (int) $item['records']),
            ], $this->repository->topRegions()),
        ];
    }

    /**
     * @param  array{active: int, paused: int, failed: int, other: int}  $statusCounts
     * @return list<array{label: string, count: int, percent: int, color: string}>
     */
    private function buildBreakdown(array $statusCounts): array
    {
        $total = array_sum($statusCounts);

        return array_map(function (string $status) use ($statusCounts, $total): array {
            $count = max(0, (int) ($statusCounts[$status] ?? 0));

            return [
                'label' => ucfirst($status),
                'count' => $count,
                'percent' => $this->percentage($count, $total),
                'color' => self::STATUS_COLORS[$status],
            ];
        }, self::ALLOWED_STATUSES);
    }

    /**
     * @param  array{record_key: string, name: string, phone: string, module: string, status: string, region: string, updated: string}  $item
     * @return array{record_key: string, name: string, phone: string, module: string, status: string, region: string, updated: string}
     */
    private function normalizeActivity(array $item): array
    {
        $status = in_array($item['status'], self::ALLOWED_STATUSES, true) ? $item['status'] : 'other';

        return [
            'record_key' => (string) $item['record_key'],
            'name' => (string) $item['name'],
            'phone' => (string) $item['phone'],
            'module' => (string) $item['module'],
            'status' => $status,
            'region' => (string) $item['region'],
            'updated' => (string) $item['updated'],
        ];
    }

    private function percentage(int $count, int $total): int
    {
        if ($total <= 0) {
            return 0;
        }

        $value = (int) round(($count / $total) * 100);

        return max(0, min(100, $value));
    }
}
