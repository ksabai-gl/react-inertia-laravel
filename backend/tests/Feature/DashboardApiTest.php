<?php

use App\Services\DashboardService;
use Mockery;
use RuntimeException;

afterEach(function () {
    Mockery::close();
});

test('dashboard api returns json summary', function () {
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
});

test('dashboard page is powered by the same php data', function () {
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

test('dashboard api returns standardized error envelope on service failures', function () {
    $dashboard = Mockery::mock(DashboardService::class);
    $dashboard->shouldReceive('summary')->once()->andThrow(new RuntimeException('boom'));

    app()->instance(DashboardService::class, $dashboard);

    $this->getJson('/api/dashboard')
        ->assertStatus(500)
        ->assertJsonPath('success', false)
        ->assertJsonPath('error.code', 'DASHBOARD_SUMMARY_UNAVAILABLE')
        ->assertJsonPath('error.message', 'Unable to load dashboard summary.');
});

test('dashboard api applies rate limiting with standardized envelope', function () {
    foreach (range(1, 60) as $_) {
        $this->getJson('/api/dashboard')->assertOk();
    }

    $this->getJson('/api/dashboard')
        ->assertStatus(429)
        ->assertJsonPath('success', false)
        ->assertJsonPath('error.code', 'RATE_LIMITED')
        ->assertJsonPath('error.message', 'Too many requests. Please try again later.');
});
