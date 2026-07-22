<?php

namespace App\Repositories;

use App\Models\DashboardRecord;

class DashboardRepository
{
    /**
     * @return list<array{record_key: string, name: string, phone: string, module: string, status: string, region: string, updated: string}>
     */
    public function activity(int $limit = 10): array
    {
        return DashboardRecord::query()
            ->orderByDesc('updated_at_source')
            ->limit($limit)
            ->get(['record_key', 'name', 'phone', 'module', 'status', 'region', 'updated_at_source'])
            ->map(fn (DashboardRecord $record): array => [
                'record_key' => (string) $record->record_key,
                'name' => (string) $record->name,
                'phone' => (string) $record->phone,
                'module' => (string) $record->module,
                'status' => (string) $record->status,
                'region' => (string) $record->region,
                'updated' => $record->updated_at_source?->format('d M, H:i') ?? now()->format('d M, H:i'),
            ])
            ->values()
            ->all();
    }

    /**
     * @return array{active: int, paused: int, failed: int, other: int}
     */
    public function statusCounts(): array
    {
        $rows = DashboardRecord::query()
            ->selectRaw('status, COUNT(*) as aggregate')
            ->groupBy('status')
            ->pluck('aggregate', 'status')
            ->all();

        $counts = [
            'active' => 0,
            'paused' => 0,
            'failed' => 0,
            'other' => 0,
        ];

        foreach ($rows as $status => $count) {
            $normalizedStatus = in_array($status, ['active', 'paused', 'failed', 'other'], true)
                ? $status
                : 'other';

            $counts[$normalizedStatus] += (int) $count;
        }

        return $counts;
    }

    /**
     * @return list<array{region: string, records: int}>
     */
    public function topRegions(int $limit = 5): array
    {
        return DashboardRecord::query()
            ->selectRaw('region, COUNT(*) as records')
            ->groupBy('region')
            ->orderByDesc('records')
            ->orderBy('region')
            ->limit($limit)
            ->get()
            ->map(fn (DashboardRecord $record): array => [
                'region' => (string) $record->region,
                'records' => max(0, (int) $record->records),
            ])
            ->values()
            ->all();
    }

    public function totalRecords(): int
    {
        return DashboardRecord::query()->count();
    }

    public function activeRecords(): int
    {
        return DashboardRecord::query()->where('status', 'active')->count();
    }

    public function failedRecords(): int
    {
        return DashboardRecord::query()->where('status', 'failed')->count();
    }

    public function uniquePhoneCount(): int
    {
        return DashboardRecord::query()->distinct('phone')->count('phone');
    }

    public function regionCount(): int
    {
        return DashboardRecord::query()->distinct('region')->count('region');
    }
}
