<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

test('dashboard api returns json summary contract', function () {
    // ISO-8601 / AC-A01, AC-A02, AC-A03
    $response = $this->getJson('/api/dashboard');

    $response
        ->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonPath('meta.source', 'php-api')
        ->assertJsonStructure([
            'success',
            'data' => [
                'stats' => [
                    ['key', 'label', 'value', 'hint'],
                ],
                'activity' => [
                    ['name', 'phone', 'module', 'status', 'region', 'updated'],
                ],
                'breakdown' => [
                    ['label', 'count', 'percent', 'color'],
                ],
                'regions' => [
                    ['region', 'records'],
                ],
            ],
            'meta' => [
                'generated_at',
                'source',
            ],
        ]);

    expect($response->json('data.stats'))->toHaveCount(4);
    expect($response->json('data.activity'))->toHaveCount(10);

    expect($response->json('data.stats.0.value'))->toBe('10');
    expect($response->json('data.stats.1.value'))->toBe('10');
    expect($response->json('data.stats.2.value'))->toBe('2');
    expect($response->json('data.stats.3.value'))->toBe('10');
});

test('dashboard api breakdown is mathematically consistent with activity', function () {
    // ISO-8601 / AC-A01, AC-A02, analysis validation_classifications.business[0]
    $response = $this->getJson('/api/dashboard')->assertOk();

    $activity = $response->json('data.activity');
    $breakdown = $response->json('data.breakdown');

    $statusCounts = array_count_values(array_column($activity, 'status'));
    $countByLabel = [];
    foreach ($breakdown as $item) {
        $countByLabel[strtolower($item['label'])] = $item['count'];
    }

    expect($countByLabel['active'] ?? 0)->toBe($statusCounts['active'] ?? 0);
    expect($countByLabel['paused'] ?? 0)->toBe($statusCounts['paused'] ?? 0);
    expect($countByLabel['failed'] ?? 0)->toBe($statusCounts['failed'] ?? 0);
    expect($countByLabel['other'] ?? 0)->toBe($statusCounts['other'] ?? 0);

    $totalFromBreakdown = array_sum(array_column($breakdown, 'count'));
    expect($totalFromBreakdown)->toBe(count($activity));

    $percentSum = array_sum(array_column($breakdown, 'percent'));
    expect($percentSum)->toBe(100);
});

test('dashboard api applies throttle middleware', function () {
    // ISO-8601 / AC-A04, analysis security_findings[0]
    $route = Route::getRoutes()->match(Request::create('/api/dashboard', 'GET'));

    expect($route->gatherMiddleware())->toContain('throttle:api');
});

test('dashboard page is powered by the same php data', function () {
    // ISO-8601 / regression guard
    $this->get('/')
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('Dashboard', false)
            ->has('stats', 4)
            ->has('activity', 10)
            ->has('breakdown', 4)
            ->has('regions', 5)
        );
});
