<?php

test('dashboard api returns json summary', function () {
    $response = $this->getJson('/api/dashboard');

    $response
        ->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonPath('meta.source', 'php-api')
        ->assertJsonPath('data.stats.0.value', '10')
        ->assertJsonPath('data.stats.2.value', '4')
        ->assertJsonPath('data.breakdown.0.count', 6)
        ->assertJsonPath('data.breakdown.1.count', 2)
        ->assertJsonPath('data.breakdown.2.count', 2)
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

    $percentTotal = array_sum(array_column($response->json('data.breakdown'), 'percent'));
    expect($percentTotal)->toBe(100);
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
