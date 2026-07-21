<?php

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

test('user can terminate other sessions with correct password', function () {
    config(['session.driver' => 'database']);

    $user = User::factory()->create();

    $this->actingAs($user)->get('/account/security')->assertOk();

    $currentId = session()->getId();

    DB::table('sessions')->insert([
        'id' => 'other-session-id-1',
        'user_id' => $user->id,
        'ip_address' => '127.0.0.1',
        'user_agent' => 'OtherAgent/1.0',
        'payload' => 'secret-payload-should-not-leak',
        'last_activity' => now()->timestamp,
    ]);

    $response = $this
        ->actingAs($user)
        ->delete('/account/sessions/others', [
            'password' => 'password',
        ]);

    $response->assertRedirect();
    $this->assertDatabaseMissing('sessions', ['id' => 'other-session-id-1']);
    $this->assertDatabaseHas('sessions', ['id' => $currentId]);
});

test('wrong password does not terminate other sessions', function () {
    config(['session.driver' => 'database']);

    $user = User::factory()->create();

    $this->actingAs($user)->get('/account/security')->assertOk();

    DB::table('sessions')->insert([
        'id' => 'other-session-id-2',
        'user_id' => $user->id,
        'ip_address' => '127.0.0.1',
        'user_agent' => 'OtherAgent/1.0',
        'payload' => 'secret',
        'last_activity' => now()->timestamp,
    ]);

    $response = $this
        ->actingAs($user)
        ->from('/account/security')
        ->delete('/account/sessions/others', [
            'password' => 'wrong-password',
        ]);

    $response->assertSessionHasErrors('password');
    $this->assertDatabaseHas('sessions', ['id' => 'other-session-id-2']);
});

test('user cannot terminate the current session', function () {
    config(['session.driver' => 'database']);

    $user = User::factory()->create();

    $this->actingAs($user)->get('/account/security')->assertOk();

    $currentId = session()->getId();

    $response = $this
        ->actingAs($user)
        ->from('/account/security')
        ->delete('/account/sessions/'.$currentId, [
            'password' => 'password',
        ]);

    $response->assertSessionHasErrors([
        'session' => 'You cannot terminate your current session.',
    ]);
});

test('user can terminate a specific other session', function () {
    config(['session.driver' => 'database']);

    $user = User::factory()->create();

    $this->actingAs($user)->get('/account/security')->assertOk();

    DB::table('sessions')->insert([
        'id' => 'other-session-id-3',
        'user_id' => $user->id,
        'ip_address' => '10.0.0.1',
        'user_agent' => 'OtherAgent/2.0',
        'payload' => 'secret',
        'last_activity' => now()->timestamp,
    ]);

    $response = $this
        ->actingAs($user)
        ->delete('/account/sessions/other-session-id-3', [
            'password' => 'password',
        ]);

    $response->assertRedirect();
    $this->assertDatabaseMissing('sessions', ['id' => 'other-session-id-3']);
});

test('guests cannot terminate sessions', function () {
    $response = $this->delete('/account/sessions/others', [
        'password' => 'password',
    ]);

    $response->assertRedirect(route('login'));
});

test('password update stores a single-verifiable hash', function () {
    $user = User::factory()->create();

    $this
        ->actingAs($user)
        ->put('/user/password', [
            'current_password' => 'password',
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ])
        ->assertSessionHasNoErrors();

    expect(Hash::check('new-password', $user->refresh()->password))->toBeTrue();
});
