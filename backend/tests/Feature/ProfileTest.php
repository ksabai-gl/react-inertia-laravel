<?php

use App\Models\User;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

test('profile page is displayed', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->get('/account/profile');

    $response->assertOk();
});

test('profile information can be updated', function () {
    Notification::fake();

    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->patch('/account/profile', [
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect('/account/profile');

    $user->refresh();

    $this->assertSame('Test User', $user->name);
    $this->assertSame('test@example.com', $user->email);
    $this->assertNull($user->email_verified_at);
    Notification::assertSentTo($user, VerifyEmail::class);
});

test('email verification status is unchanged when the email address is unchanged', function () {
    Notification::fake();

    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->patch('/account/profile', [
            'name' => 'Test User',
            'email' => $user->email,
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect('/account/profile');

    $this->assertNotNull($user->refresh()->email_verified_at);
    Notification::assertNothingSent();
});

test('user can delete their account', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->delete('/account/profile', [
            'password' => 'password',
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect('/');

    $this->assertGuest();
    $this->assertNull($user->fresh());
});

test('correct password must be provided to delete account', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->from('/account/profile')
        ->delete('/account/profile', [
            'password' => 'wrong-password',
        ]);

    $response
        ->assertSessionHasErrors('password')
        ->assertRedirect('/account/profile');

    $this->assertNotNull($user->fresh());
});

test('security page is displayed without session payload', function () {
    config(['session.driver' => 'database']);

    $user = User::factory()->create();

    $this->actingAs($user)->get('/account/security')->assertOk();

    DB::table('sessions')->insert([
        'id' => 'security-payload-probe',
        'user_id' => $user->id,
        'ip_address' => '127.0.0.1',
        'user_agent' => 'ProbeAgent/1.0',
        'payload' => 'LEAK_MARKER_PAYLOAD_SHOULD_NOT_APPEAR',
        'last_activity' => now()->timestamp,
    ]);

    $response = $this
        ->actingAs($user)
        ->get('/account/security');

    $response->assertOk();
    expect($response->getContent())->not->toContain('LEAK_MARKER_PAYLOAD_SHOULD_NOT_APPEAR');
});
