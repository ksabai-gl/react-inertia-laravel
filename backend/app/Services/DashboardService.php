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
        return [
            'stats' => $this->stats(),
            'activity' => $this->activity(),
            'breakdown' => $this->breakdown(),
            'regions' => $this->regions(),
        ];
    }

    /**
     * @return list<array{key: string, label: string, value: string, hint: string}>
     */
    private function stats(): array
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
    private function activity(): array
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
    private function breakdown(): array
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
    private function regions(): array
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
