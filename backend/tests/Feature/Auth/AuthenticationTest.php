<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

test('guests are redirected from the dashboard to login', function () {
    $response = $this->get('/');

    $response->assertRedirect(route('login'));
});

test('authenticated verified users can view the dashboard', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('/');

    $response->assertOk();
});

// MBA-57 AC-C03 — unverified users must complete verification before dashboard
test('unverified users are redirected from the dashboard', function () {
    $user = User::factory()->unverified()->create();

    $response = $this->actingAs($user)->get('/');

    $response->assertRedirect(route('verification.notice'));
});

test('legacy dashboard url redirects to home', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('/dashboard');

    $response->assertRedirect('/');
});

test('login screen can be rendered', function () {
    $response = $this->get('/login');

    $response->assertOk();
});

test('users can authenticate using the login screen', function () {
    $user = User::factory()->create();

    $response = $this->post('/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('dashboard', absolute: false));
});

test('users cannot authenticate with invalid password', function () {
    $user = User::factory()->create();

    $this->post('/login', [
        'email' => $user->email,
        'password' => 'wrong-password',
    ]);

    $this->assertGuest();
});

// MBA-57 AC-A03 — legacy double-hashed passwords fail login; recover via reset
test('legacy double-hashed passwords fail login', function () {
    $user = User::factory()->create([
        'password' => Hash::make(Hash::make('password')),
    ]);

    $this->post('/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $this->assertGuest();
});

test('users can logout when authenticated', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post('/logout');

    $this->assertGuest();
    $response->assertRedirect('/');
});

test('register then login succeeds with a single-hashed password', function () {
    $this->post('/register', [
        'name' => 'Round Trip User',
        'email' => 'roundtrip@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ])->assertRedirect(route('dashboard', absolute: false));

    $this->assertAuthenticated();

    $user = User::where('email', 'roundtrip@example.com')->first();
    expect($user)->not->toBeNull();
    expect(Hash::check('password', $user->password))->toBeTrue();

    $this->post('/logout');
    $this->assertGuest();

    $this->post('/login', [
        'email' => 'roundtrip@example.com',
        'password' => 'password',
    ])->assertRedirect(route('dashboard', absolute: false));

    $this->assertAuthenticated();
});
