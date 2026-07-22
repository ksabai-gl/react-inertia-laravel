<?php

use Database\Seeders\DashboardRecordSeeder;
use Illuminate\Support\Facades\Hash;

test('dashboard api blocks unauthenticated requests when token mode is enabled', function () {
    // MBA-65 AC-A01 / security finding: protect dashboard API route.
    config()->set('dashboard.access_mode', 'token');
    config()->set('dashboard.api_token', 'test-dashboard-token');

    $response = $this->getJson('/api/dashboard');

    $response
        ->assertUnauthorized()
        ->assertJsonPath('success', false)
        ->assertJsonPath('error.code', 'DASHBOARD_UNAUTHORIZED');
});

test('dashboard api returns contract-safe summary when authorized', function () {
    // MBA-65 AC-A03, AC-A04 / contract and field-level validation coverage.
    config()->set('dashboard.access_mode', 'token');
    config()->set('dashboard.api_token', 'test-dashboard-token');

    $this->seed(DashboardRecordSeeder::class);

    $response = $this->withToken('test-dashboard-token')->getJson('/api/dashboard');

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
                    ['record_key', 'name', 'phone', 'module', 'status', 'region', 'updated'],
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

    foreach ($response->json('data.activity') as $activity) {
        expect(['active', 'paused', 'failed', 'other'])->toContain($activity['status']);
    }

    foreach ($response->json('data.breakdown') as $breakdown) {
        expect((int) $breakdown['percent'])->toBeGreaterThanOrEqual(0)->toBeLessThanOrEqual(100);
    }

    foreach ($response->json('data.regions') as $region) {
        expect((int) $region['records'])->toBeGreaterThanOrEqual(0);
    }
});

test('dashboard api emits iso-8601 generated_at metadata', function () {
    // MBA-65 AC-A03 / response metadata contract enforcement.
    config()->set('dashboard.access_mode', 'token');
    config()->set('dashboard.api_token', 'test-dashboard-token');

    $this->seed(DashboardRecordSeeder::class);

    $response = $this->withToken('test-dashboard-token')->getJson('/api/dashboard');

    $response->assertOk();

    $generatedAt = (string) $response->json('meta.generated_at');
    $parsed = \DateTimeImmutable::createFromFormat(DATE_ATOM, $generatedAt);

    expect($generatedAt)->not->toBe('');
    expect($parsed)->not->toBeFalse();
});

test('dashboard page requires basic auth when basic mode is enabled', function () {
    // MBA-65 AC-A01 / security finding: protect dashboard web route.
    config()->set('dashboard.access_mode', 'basic');
    config()->set('dashboard.basic_user', 'dashboard-user');
    config()->set('dashboard.basic_pass_hash', Hash::make('dashboard-password'));

    $this->get('/')->assertStatus(401);

    $this->seed(DashboardRecordSeeder::class);

    $credentials = base64_encode('dashboard-user:dashboard-password');

    $this->withHeader('Authorization', "Basic {$credentials}")
        ->get('/')
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('Dashboard', false)
            ->has('stats', 4)
            ->has('activity', 10)
            ->has('breakdown', 4)
            ->has('regions', 5)
        );
});

test('dashboard redirect route is also protected and redirects after auth', function () {
    // MBA-65 AC-A01 / regression: /dashboard remains behind middleware.
    config()->set('dashboard.access_mode', 'basic');
    config()->set('dashboard.basic_user', 'dashboard-user');
    config()->set('dashboard.basic_pass_hash', Hash::make('dashboard-password'));

    $this->get('/dashboard')->assertStatus(401);

    $credentials = base64_encode('dashboard-user:dashboard-password');

    $this->withHeader('Authorization', "Basic {$credentials}")
        ->get('/dashboard')
        ->assertRedirect('/');
});
