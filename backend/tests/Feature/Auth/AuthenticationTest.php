<?php

use App\Models\User;

test('dashboard is the landing page', function () {
    $response = $this->get('/');

    $response->assertStatus(200);
});

test('legacy dashboard url redirects to home', function () {
    $response = $this->get('/dashboard');

    $response->assertRedirect('/');
});

test('login endpoint is not available', function () {
    $user = User::factory()->create();

    $response = $this->post('/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $response->assertNotFound();
    $this->assertGuest();
});

test('users can logout when authenticated', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post('/logout');

    $this->assertGuest();
    $response->assertRedirect('/');
});
