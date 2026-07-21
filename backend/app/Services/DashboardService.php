<?php

namespace App\Services;

class DashboardService
{
    /**
     * @return array{
     *     stats: list<array{key: string, label: string, value: string, hint: string}>,
     *     activity: list<array{name: string, phone: string, module: string, status: string, region: string, updated: string}>,
     *     breakdown: list<array{label: string, count: int, percent: int, color: string}>,
     *     regions: list<array{region: string, records: int}>
     * }
     */
    public function summary(): array
    {
        $activity = [
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

        $statusCounts = [
            'active' => 0,
            'paused' => 0,
            'failed' => 0,
            'other' => 0,
        ];

        $regionCounts = [];

        foreach ($activity as $record) {
            $status = $record['status'];

            if (! array_key_exists($status, $statusCounts)) {
                $status = 'other';
            }

            $statusCounts[$status]++;
            $regionCounts[$record['region']] = ($regionCounts[$record['region']] ?? 0) + 1;
        }

        $totalRecords = count($activity);
        $openAlerts = $statusCounts['paused'] + $statusCounts['failed'];

        $toPercent = static fn (int $count): int => $totalRecords === 0
            ? 0
            : (int) round(($count / $totalRecords) * 100);

        $activePercent = $toPercent($statusCounts['active']);
        $pausedPercent = $toPercent($statusCounts['paused']);
        $failedPercent = $toPercent($statusCounts['failed']);
        $otherPercent = max(0, 100 - ($activePercent + $pausedPercent + $failedPercent));

        $regions = array_map(
            static fn (string $region, int $records): array => [
                'region' => $region,
                'records' => $records,
            ],
            array_keys($regionCounts),
            array_values($regionCounts),
        );

        usort(
            $regions,
            static fn (array $left, array $right): int => ($right['records'] <=> $left['records'])
                ?: strcmp($left['region'], $right['region']),
        );

        return [
            'stats' => [
                [
                    'key' => 'records',
                    'label' => 'Total Test Records',
                    'value' => (string) $totalRecords,
                    'hint' => sprintf('%d active', $statusCounts['active']),
                ],
                [
                    'key' => 'phones',
                    'label' => 'Phone Numbers',
                    'value' => (string) $totalRecords,
                    'hint' => 'Monitored globally',
                ],
                [
                    'key' => 'alerts',
                    'label' => 'Open Alerts',
                    'value' => (string) $openAlerts,
                    'hint' => 'Require attention',
                ],
                [
                    'key' => 'countries',
                    'label' => 'Countries',
                    'value' => (string) count($regionCounts),
                    'hint' => 'Coverage regions',
                ],
            ],
            'activity' => $activity,
            'breakdown' => [
                ['label' => 'Active', 'count' => $statusCounts['active'], 'percent' => $activePercent, 'color' => '#10b981'],
                ['label' => 'Paused', 'count' => $statusCounts['paused'], 'percent' => $pausedPercent, 'color' => '#fb923c'],
                ['label' => 'Failed', 'count' => $statusCounts['failed'], 'percent' => $failedPercent, 'color' => '#ef4444'],
                ['label' => 'Other', 'count' => $statusCounts['other'], 'percent' => $otherPercent, 'color' => '#d4d4d8'],
            ],
            'regions' => array_slice($regions, 0, 5),
        ];
    }
}
