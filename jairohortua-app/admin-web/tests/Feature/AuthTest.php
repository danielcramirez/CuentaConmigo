<?php

use App\Models\User;
use Laravel\Sanctum\Sanctum;

test('login with valid credentials', function () {
    $user = User::create([
        'username' => 'testuser',
        'email' => 'test@example.com',
        'password' => bcrypt('password123'),
        'referral_code' => 'TEST0001',
    ]);

    $response = $this->postJson('/api/auth/login', [
        'username' => 'testuser',
        'password' => 'password123',
    ]);

    $response->assertStatus(200)
        ->assertJsonStructure(['access_token', 'token_type', 'user', 'roles', 'modules']);
});

test('login fails with invalid credentials', function () {
    User::create([
        'username' => 'testuser',
        'email' => 'test@example.com',
        'password' => bcrypt('password123'),
        'referral_code' => 'TEST0001',
    ]);

    $response = $this->postJson('/api/auth/login', [
        'username' => 'testuser',
        'password' => 'wrongpassword',
    ]);

    $response->assertStatus(401)
        ->assertJson(['message' => 'Invalid credentials']);
});

test('refresh token rotates token', function () {
    $user = User::create([
        'username' => 'testuser',
        'email' => 'test@example.com',
        'password' => bcrypt('password123'),
        'referral_code' => 'TEST0001',
    ]);

    Sanctum::actingAs($user);

    $oldToken = $user->currentAccessToken()->plainTextToken;

    $response = $this->postJson('/api/auth/refresh');

    $response->assertStatus(200)
        ->assertJsonStructure(['access_token', 'token_type'])
        ->assertJson(['message' => 'Token refreshed']);

    $newToken = $response->json('access_token');
    $this->assertNotEquals($oldToken, $newToken);
});

test('logout revokes token', function () {
    $user = User::create([
        'username' => 'testuser',
        'email' => 'test@example.com',
        'password' => bcrypt('password123'),
        'referral_code' => 'TEST0001',
    ]);

    Sanctum::actingAs($user);

    $response = $this->postJson('/api/auth/logout');

    $response->assertStatus(200)
        ->assertJson(['message' => 'Logged out successfully']);

    // Intentar usar el token revocado debería fallar
    $this->postJson('/api/auth/logout')
        ->assertStatus(401);
});
