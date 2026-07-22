<?php

namespace Database\Seeders;

use Carbon\CarbonImmutable;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DashboardRecordSeeder extends Seeder
{
    /**
     * Seed dashboard records used by the dashboard summary.
     */
    public function run(): void
    {
        $baseDate = CarbonImmutable::create(2026, 6, 17, 13, 10, 0, 'UTC');

        $records = [
            ['record_key' => 'us-tollfree-regression', 'name' => 'US Toll-Free Regression Suite', 'phone' => '+1 (800) 555-0101', 'module' => 'Regression Tests', 'status' => 'active', 'region' => 'US', 'updated_at_source' => $baseDate],
            ['record_key' => 'uk-retail-ivr-discovery', 'name' => 'UK Retail IVR Discovery', 'phone' => '+44 20 7946 0958', 'module' => 'Discovery Scans', 'status' => 'active', 'region' => 'GB', 'updated_at_source' => $baseDate->subMinutes(25)],
            ['record_key' => 'apac-banking-path-map', 'name' => 'APAC Banking Path Map', 'phone' => '+65 6123 4567', 'module' => 'Discovery Scans', 'status' => 'paused', 'region' => 'SG', 'updated_at_source' => $baseDate->subHours(1)->subMinutes(50)],
            ['record_key' => 'de-support-line-regression', 'name' => 'DE Support Line Regression', 'phone' => '+49 30 123456', 'module' => 'Regression Tests', 'status' => 'failed', 'region' => 'DE', 'updated_at_source' => $baseDate->subDay()->subHours(19)->subMinutes(5)],
            ['record_key' => 'india-prepaid-menu-audit', 'name' => 'India Prepaid Menu Audit', 'phone' => '+91 22 4000 1234', 'module' => 'Regression Tests', 'status' => 'active', 'region' => 'IN', 'updated_at_source' => $baseDate->subDay()->subHours(20)->subMinutes(30)],
            ['record_key' => 'brazil-collections-discovery', 'name' => 'Brazil Collections Discovery', 'phone' => '+55 11 4002 8922', 'module' => 'Discovery Scans', 'status' => 'paused', 'region' => 'BR', 'updated_at_source' => $baseDate->subDay()->subHours(22)->subMinutes(55)],
            ['record_key' => 'ca-enterprise-ivr-suite', 'name' => 'CA Enterprise IVR Suite', 'phone' => '+1 (416) 555-0199', 'module' => 'Regression Tests', 'status' => 'active', 'region' => 'CA', 'updated_at_source' => $baseDate->subDay()->subHours(27)->subMinutes(40)],
            ['record_key' => 'fr-hotel-booking-paths', 'name' => 'FR Hotel Booking Paths', 'phone' => '+33 1 42 68 53 00', 'module' => 'Discovery Scans', 'status' => 'active', 'region' => 'FR', 'updated_at_source' => $baseDate->subDays(2)->subHours(16)],
            ['record_key' => 'au-telco-regression-pack', 'name' => 'AU Telco Regression Pack', 'phone' => '+61 2 9876 5432', 'module' => 'Regression Tests', 'status' => 'failed', 'region' => 'AU', 'updated_at_source' => $baseDate->subDays(2)->subHours(19)->subMinutes(15)],
            ['record_key' => 'jp-customer-care-scan', 'name' => 'JP Customer Care Scan', 'phone' => '+81 3 1234 5678', 'module' => 'Discovery Scans', 'status' => 'active', 'region' => 'JP', 'updated_at_source' => $baseDate->subDays(2)->subHours(28)->subMinutes(50)],
        ];

        $now = now();

        DB::table('dashboard_records')->upsert(
            array_map(static fn (array $record): array => [
                ...$record,
                'created_at' => $now,
                'updated_at' => $now,
            ], $records),
            ['record_key'],
            ['name', 'phone', 'module', 'status', 'region', 'updated_at_source', 'updated_at']
        );
    }
}
