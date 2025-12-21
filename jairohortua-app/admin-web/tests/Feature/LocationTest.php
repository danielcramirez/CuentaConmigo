<?php

use App\Models\User;
use App\Models\Event;
use App\Models\UserLocation;
use Laravel\Sanctum\Sanctum;
use Carbon\Carbon;

test('location can be recorded', function () {
    $user = User::create([
        'username' => 'testuser',
        'email' => 'test@example.com',
        'password' => bcrypt('password123'),
        'referral_code' => 'TEST0001',
    ]);

    Sanctum::actingAs($user);

    $response = $this->postJson('/api/location', [
        'latitude' => 40.7128,
        'longitude' => -74.0060,
        'accuracy' => 20.5,
        'timestamp' => now()->toIso8601String(),
    ]);

    $response->assertStatus(201)
        ->assertJsonStructure(['id', 'user_id', 'latitude', 'longitude', 'created_at']);

    $this->assertDatabaseHas('user_locations', [
        'user_id' => $user->id,
        'latitude' => 40.7128,
        'longitude' => -74.0060,
    ]);
});

test('location accepts iso8601 timestamp with offset', function () {
    $user = User::create([
        'username' => 'testuser2',
        'email' => 'test2@example.com',
        'password' => bcrypt('password123'),
        'referral_code' => 'TEST0002',
    ]);

    Sanctum::actingAs($user);

    $response = $this->postJson('/api/location', [
        'latitude' => 10.0,
        'longitude' => -10.0,
        'timestamp' => '2025-12-21T10:30:00-05:00',
    ]);

    $response->assertStatus(201);
});

test('location validation fails with invalid coordinates', function () {
    $user = User::create([
        'username' => 'testuser',
        'email' => 'test@example.com',
        'password' => bcrypt('password123'),
        'referral_code' => 'TEST0001',
    ]);

    Sanctum::actingAs($user);

    $response = $this->postJson('/api/location', [
        'latitude' => 200, // Invalid (> 90)
        'longitude' => -74.0060,
    ]);

    $response->assertStatus(422);
});
