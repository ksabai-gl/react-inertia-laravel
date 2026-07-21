<?php

test('dashboard api returns json summary with metadata contract', function () {
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

test('dashboard api validates query parameters', function () {
    $response = $this->getJson('/api/dashboard?status=unknown&per_page=0');

    $response
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['status', 'per_page']);
});

test('dashboard api supports deterministic status filtering', function () {
    $response = $this->getJson('/api/dashboard?status=paused');

    $response->assertOk();

    $activity = $response->json('data.activity');

    expect($activity)->toHaveCount(2);

    foreach ($activity as $row) {
        expect($row['status'])->toBe('paused');
    }
});

test('dashboard api supports optional pagination metadata', function () {
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

test('dashboard api rejects unauthenticated requests when auth enforcement is enabled', function () {
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
