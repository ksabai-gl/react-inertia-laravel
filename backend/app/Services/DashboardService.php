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
        $normalizedActivity = [];

        foreach ($activity as $row) {
            $status = $row['status'];
            if (! array_key_exists($status, $statusCounts)) {
                $status = 'other';
            }

            $row['status'] = $status;
            $normalizedActivity[] = $row;
            $statusCounts[$status]++;

            $region = $row['region'];
            if (! isset($regionCounts[$region])) {
                $regionCounts[$region] = 0;
            }
            $regionCounts[$region]++;
        }

        $recordCount = count($normalizedActivity);
        $alertsCount = $statusCounts['failed'] + $statusCounts['other'];
        $uniquePhones = count(array_unique(array_column($normalizedActivity, 'phone')));
        $countryCount = count($regionCounts);

        $breakdown = [
            ['label' => 'Active', 'count' => $statusCounts['active'], 'percent' => 0, 'color' => '#10b981'],
            ['label' => 'Paused', 'count' => $statusCounts['paused'], 'percent' => 0, 'color' => '#fb923c'],
            ['label' => 'Failed', 'count' => $statusCounts['failed'], 'percent' => 0, 'color' => '#ef4444'],
            ['label' => 'Other', 'count' => $statusCounts['other'], 'percent' => 0, 'color' => '#d4d4d8'],
        ];

        if ($recordCount > 0) {
            $allocated = 0;
            $lastIndex = count($breakdown) - 1;

            foreach ($breakdown as $index => &$item) {
                if ($index === $lastIndex) {
                    $item['percent'] = 100 - $allocated;
                    continue;
                }

                $item['percent'] = (int) floor(($item['count'] * 100) / $recordCount);
                $allocated += $item['percent'];
            }
            unset($item);
        }

        arsort($regionCounts);
        $regions = [];
        foreach ($regionCounts as $region => $records) {
            $regions[] = [
                'region' => $region,
                'records' => $records,
            ];

            if (count($regions) === 5) {
                break;
            }
        }

        return [
            'stats' => [
                [
                    'key' => 'records',
                    'label' => 'Total Test Records',
                    'value' => (string) $recordCount,
                    'hint' => $statusCounts['active'].' active',
                ],
                [
                    'key' => 'phones',
                    'label' => 'Phone Numbers',
                    'value' => (string) $uniquePhones,
                    'hint' => 'Monitored globally',
                ],
                [
                    'key' => 'alerts',
                    'label' => 'Open Alerts',
                    'value' => (string) $alertsCount,
                    'hint' => 'Require attention',
                ],
                [
                    'key' => 'countries',
                    'label' => 'Countries',
                    'value' => (string) $countryCount,
                    'hint' => 'Coverage regions',
                ],
            ],
            'activity' => $normalizedActivity,
            'breakdown' => $breakdown,
            'regions' => $regions,
        ];
    }
}
