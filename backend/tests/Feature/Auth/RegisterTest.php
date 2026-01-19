<?php

use App\Models\User;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Notifications\Notification as BaseNotification;

uses(RefreshDatabase::class);

function validRegisterPayload(array $overrides = []): array
{
    return array_merge([
        'name' => 'Ali',
        'email' => 'ali123123@test.com',
        'password' => 'password123',
        'role' => 1, // optional (1,2,3)
    ], $overrides);
}

test('register succeeds and returns 201 with accessToken + user and stores refresh token in DB', function () {

    // Arrange
    $payload = validRegisterPayload();

    // Act
    $response = $this->postJson('/api/auth/register', $payload);

    // Assert (status + structure)
    $response->assertStatus(201);

    $response->assertJsonStructure([
        'isSuccess',
        'data' => [
            'accessToken',
            'user',
        ],
    ]);

    $response->assertJsonPath('isSuccess', true);
    $response->assertJsonPath('data.user.email', 'ali123123@test.com');

    // Assert (DB: user created)
    $this->assertDatabaseHas('users', ['email' => 'ali123123@test.com']);

    // Assert (DB: refresh token stored)
    $user = User::where('email', 'ali123123@test.com')->firstOrFail();
    expect($user->refresh_token)->not->toBeNull();
    expect($user->refresh_token_expiration_at)->not->toBeNull();

    // Assert (verification notification triggered)
});

test('register fails with 422 if name is missing', function () {

    // Arrange
    $payload = validRegisterPayload(['name' => null]);

    // Act
    $response = $this->postJson('/api/auth/register', $payload);

    // Assert
    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['name']);
});

test('register fails with 422 if email is missing', function () {

    // Arrange
    $payload = validRegisterPayload();
    unset($payload['email']);

    // Act
    $response = $this->postJson('/api/auth/register', $payload);

    // Assert
    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['email']);
});

test('register fails with 422 if password is too short', function () {

    // Arrange
    $payload = validRegisterPayload(['password' => '123']); // < 6

    // Act
    $response = $this->postJson('/api/auth/register', $payload);

    // Assert
    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['password']);
});

test('register fails with 422 if role is not in 1,2,3', function () {

    // Arrange
    $payload = validRegisterPayload(['role' => 99]);

    // Act
    $response = $this->postJson('/api/auth/register', $payload);

    // Assert
    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['role']);
});

test('register fails with 422 if email is already taken (duplicate email)', function () {

    // Arrange
    // Requires HasFactory trait on User model (see below).
    User::factory()->create(['email' => 'ali123123@test.com']);

    $payload = validRegisterPayload(['email' => 'ali123123@test.com']);

    // Act
    $response = $this->postJson('/api/auth/register', $payload);

    // Assert
    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['email']);
});
