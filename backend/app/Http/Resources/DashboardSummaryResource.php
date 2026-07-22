<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use UnexpectedValueException;

/**
 * @mixin array{
 *     stats: list<array{key: string, label: string, value: string, hint: string}>,
 *     activity: list<array{record_key: string, name: string, phone: string, module: string, status: string, region: string, updated: string}>,
 *     breakdown: list<array{label: string, count: int, percent: int, color: string}>,
 *     regions: list<array{region: string, records: int}>
 * }
 */
class DashboardSummaryResource extends JsonResource
{
    private const ALLOWED_STATUSES = ['active', 'paused', 'failed', 'other'];

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        if (! is_array($this->resource)) {
            throw new UnexpectedValueException('Dashboard summary must be an array.');
        }

        $stats = $this->normalizeStats($this->resource['stats'] ?? []);
        $activity = $this->normalizeActivity($this->resource['activity'] ?? []);
        $breakdown = $this->normalizeBreakdown($this->resource['breakdown'] ?? []);
        $regions = $this->normalizeRegions($this->resource['regions'] ?? []);

        return [
            'stats' => $stats,
            'activity' => $activity,
            'breakdown' => $breakdown,
            'regions' => $regions,
        ];
    }

    /**
     * @param  mixed  $stats
     * @return list<array{key: string, label: string, value: string, hint: string}>
     */
    private function normalizeStats(mixed $stats): array
    {
        if (! is_array($stats)) {
            throw new UnexpectedValueException('Dashboard stats must be a list.');
        }

        $keys = [];

        return array_map(function (mixed $item) use (&$keys): array {
            if (! is_array($item)) {
                throw new UnexpectedValueException('Each stats item must be an object.');
            }

            $key = (string) ($item['key'] ?? '');
            if ($key === '' || isset($keys[$key])) {
                throw new UnexpectedValueException('Dashboard stat keys must be unique and non-empty.');
            }

            $keys[$key] = true;

            return [
                'key' => $key,
                'label' => (string) ($item['label'] ?? ''),
                'value' => (string) ($item['value'] ?? ''),
                'hint' => (string) ($item['hint'] ?? ''),
            ];
        }, $stats);
    }

    /**
     * @param  mixed  $activity
     * @return list<array{record_key: string, name: string, phone: string, module: string, status: string, region: string, updated: string}>
     */
    private function normalizeActivity(mixed $activity): array
    {
        if (! is_array($activity)) {
            throw new UnexpectedValueException('Dashboard activity must be a list.');
        }

        return array_map(function (mixed $item): array {
            if (! is_array($item)) {
                throw new UnexpectedValueException('Each activity item must be an object.');
            }

            $status = (string) ($item['status'] ?? '');
            if (! in_array($status, self::ALLOWED_STATUSES, true)) {
                throw new UnexpectedValueException('Dashboard activity status is invalid.');
            }

            return [
                'record_key' => (string) ($item['record_key'] ?? ''),
                'name' => (string) ($item['name'] ?? ''),
                'phone' => (string) ($item['phone'] ?? ''),
                'module' => (string) ($item['module'] ?? ''),
                'status' => $status,
                'region' => (string) ($item['region'] ?? ''),
                'updated' => (string) ($item['updated'] ?? ''),
            ];
        }, $activity);
    }

    /**
     * @param  mixed  $breakdown
     * @return list<array{label: string, count: int, percent: int, color: string}>
     */
    private function normalizeBreakdown(mixed $breakdown): array
    {
        if (! is_array($breakdown)) {
            throw new UnexpectedValueException('Dashboard breakdown must be a list.');
        }

        return array_map(function (mixed $item): array {
            if (! is_array($item)) {
                throw new UnexpectedValueException('Each breakdown item must be an object.');
            }

            $percent = (int) ($item['percent'] ?? -1);
            if ($percent < 0 || $percent > 100) {
                throw new UnexpectedValueException('Dashboard breakdown percent must be between 0 and 100.');
            }

            return [
                'label' => (string) ($item['label'] ?? ''),
                'count' => max(0, (int) ($item['count'] ?? 0)),
                'percent' => $percent,
                'color' => (string) ($item['color'] ?? ''),
            ];
        }, $breakdown);
    }

    /**
     * @param  mixed  $regions
     * @return list<array{region: string, records: int}>
     */
    private function normalizeRegions(mixed $regions): array
    {
        if (! is_array($regions)) {
            throw new UnexpectedValueException('Dashboard regions must be a list.');
        }

        return array_map(function (mixed $item): array {
            if (! is_array($item)) {
                throw new UnexpectedValueException('Each regions item must be an object.');
            }

            $records = (int) ($item['records'] ?? -1);
            if ($records < 0) {
                throw new UnexpectedValueException('Dashboard region records must be non-negative.');
            }

            return [
                'region' => (string) ($item['region'] ?? ''),
                'records' => $records,
            ];
        }, $regions);
    }
}
