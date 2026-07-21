<?php

use App\Services\DashboardService;
use RuntimeException;
use UnexpectedValueException;

test('dashboard api returns json summary with metadata contract', function () {
    // MBA-61 AC-A01/AC-A03: default API contract remains stable.
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

    expect($response->json('meta.generated_at'))->toBeString()->not->toBeEmpty();
    expect($response->json('data.stats'))->toHaveCount(4);
    expect($response->json('data.activity'))->toHaveCount(10);
    expect($response->json('data.activity_meta'))->toBeNull();
});

test('dashboard api validates query parameters including boundaries', function () {
    // MBA-61 AC-A04: invalid query params return deterministic 422 validation errors.
    $response = $this->getJson('/api/dashboard?status=unknown&per_page=0&page=0&region=U1');

    $response
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['status', 'per_page', 'page', 'region']);
});

test('dashboard api normalizes region filter to uppercase', function () {
    // MBA-61 AC-A03 + analysis field_validations(default): region normalization remains explicit and testable.
    $response = $this->getJson('/api/dashboard?region=us');

    $response->assertOk();

    $activity = $response->json('data.activity');

    expect($activity)->toHaveCount(1);
    expect($activity[0]['region'])->toBe('US');
});

test('dashboard api supports deterministic status filtering', function () {
    // MBA-61 AC-A06: deterministic status filtering over known fixture payload.
    $response = $this->getJson('/api/dashboard?status=paused');

    $response->assertOk();

    $activity = $response->json('data.activity');

    expect($activity)->toHaveCount(2);

    foreach ($activity as $row) {
        expect($row['status'])->toBe('paused');
    }
});

test('dashboard api supports optional pagination metadata', function () {
    // MBA-61 AC-A06 + analysis performance_findings[0]: paginated response includes stable metadata.
    $response = $this->getJson('/api/dashboard?page=2&per_page=3');

    $response
        ->assertOk()
        ->assertJsonPath('data.activity_meta.current_page', 2)
        ->assertJsonPath('data.activity_meta.per_page', 3)
        ->assertJsonPath('data.activity_meta.last_page', 4)
        ->assertJsonPath('data.activity_meta.total', 10)
        ->assertJsonPath('data.activity_meta.from', 4)
        ->assertJsonPath('data.activity_meta.to', 6);

    expect($response->json('data.activity'))->toHaveCount(3);
});

test('dashboard api clamps page to the last available page', function () {
    // MBA-61 AC-A06: out-of-range page requests remain deterministic and bounded.
    $response = $this->getJson('/api/dashboard?page=99&per_page=3');

    $response
        ->assertOk()
        ->assertJsonPath('data.activity_meta.current_page', 4)
        ->assertJsonPath('data.activity_meta.last_page', 4)
        ->assertJsonPath('data.activity_meta.from', 10)
        ->assertJsonPath('data.activity_meta.to', 10);

    expect($response->json('data.activity'))->toHaveCount(1);
});

test('dashboard api rejects unauthenticated requests when auth enforcement is enabled', function () {
    // MBA-61 AC-A05 + analysis security_findings[0]: feature-flagged access control blocks anonymous callers.
    config()->set('services.dashboard.require_auth', true);

    $response = $this->getJson('/api/dashboard');

    $response
        ->assertUnauthorized()
        ->assertJsonPath('success', false)
        ->assertJsonPath('error.code', 'UNAUTHORIZED')
        ->assertJsonStructure([
            'success',
            'error' => ['code', 'message', 'trace_id'],
        ]);
});

test('dashboard api returns contract error envelope when service contract validation fails', function () {
    // MBA-61 AC-A04: unexpected contract state maps to DASHBOARD_CONTRACT_ERROR.
    $mock = \Mockery::mock(DashboardService::class);
    $mock->shouldReceive('summary')->once()->andThrow(new UnexpectedValueException('invalid shape'));
    app()->instance(DashboardService::class, $mock);

    $response = $this->getJson('/api/dashboard');

    $response
        ->assertStatus(500)
        ->assertJsonPath('success', false)
        ->assertJsonPath('error.code', 'DASHBOARD_CONTRACT_ERROR')
        ->assertJsonPath('error.message', 'Dashboard data contract validation failed.')
        ->assertJsonStructure([
            'success',
            'error' => ['code', 'message', 'trace_id'],
        ]);

    expect($response->json('error.trace_id'))->toBeString()->not->toBeEmpty();
});

test('dashboard api returns unavailable envelope for generic runtime failures', function () {
    // MBA-61 AC-A04: non-contract runtime failures map to DASHBOARD_UNAVAILABLE.
    $mock = \Mockery::mock(DashboardService::class);
    $mock->shouldReceive('summary')->once()->andThrow(new RuntimeException('boom'));
    app()->instance(DashboardService::class, $mock);

    $response = $this->getJson('/api/dashboard');

    $response
        ->assertStatus(500)
        ->assertJsonPath('success', false)
        ->assertJsonPath('error.code', 'DASHBOARD_UNAVAILABLE')
        ->assertJsonPath('error.message', 'Dashboard data is currently unavailable.')
        ->assertJsonStructure([
            'success',
            'error' => ['code', 'message', 'trace_id'],
        ]);
});

test('dashboard page is powered by the same php data', function () {
    // MBA-61 AC-A01/AC-A02 regression: web dashboard keeps expected Inertia shape.
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
