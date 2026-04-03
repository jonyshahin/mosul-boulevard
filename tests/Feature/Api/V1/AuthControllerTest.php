<?php

use App\Models\User;

test('login with valid credentials returns user and token', function () {
    $user = User::factory()->create(['role' => 'admin', 'is_active' => true]);

    $response = $this->postJson('/api/v1/auth/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $response->assertOk()
        ->assertJsonStructure(['user' => ['id', 'name', 'email', 'role'], 'token'])
        ->assertJsonPath('user.email', $user->email);
});

test('login with invalid credentials returns 401', function () {
    $user = User::factory()->create();

    $response = $this->postJson('/api/v1/auth/login', [
        'email' => $user->email,
        'password' => 'wrong-password',
    ]);

    $response->assertUnauthorized()
        ->assertJsonPath('message', 'Invalid credentials');
});

test('logout with valid token returns 200', function () {
    $user = User::factory()->create();
    $token = $user->createToken('test')->plainTextToken;

    $response = $this->withHeader('Authorization', 'Bearer '.$token)
        ->postJson('/api/v1/auth/logout');

    $response->assertOk()
        ->assertJsonPath('message', 'Logged out');

    expect($user->tokens()->count())->toBe(0);
});

test('logout without token returns 401', function () {
    $response = $this->postJson('/api/v1/auth/logout');

    $response->assertUnauthorized();
});
