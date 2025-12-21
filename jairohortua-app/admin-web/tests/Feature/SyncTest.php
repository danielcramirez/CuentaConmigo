<?php

use App\Models\User;
use Laravel\Sanctum\Sanctum;

test('sync pull retrieves changes since timestamp', function () {
    $user = User::create([
        'username' => 'testuser',
        'email' => 'test@example.com',
        'password' => bcrypt('password123'),
        'referral_code' => 'TEST0001',
    ]);

    Sanctum::actingAs($user);

    $since = now()->subDay()->toIso8601String();

    $response = $this->getJson("/api/sync/pull?since={$since}");

    $response->assertStatus(200)
        ->assertJsonStructure([
            'server_time',
            'changes' => [
                'events',
                'banners',
                'notifications',
                'referrals',
                'modules',
                'settings',
            ],
        ]);
});

test('sync push applies pending operations', function () {
    $user = User::create([
        'username' => 'testuser',
        'email' => 'test@example.com',
        'password' => bcrypt('password123'),
        'referral_code' => 'TEST0001',
    ]);

    Sanctum::actingAs($user);

    $response = $this->postJson('/api/sync/push', [
        'operations' => [
            [
                'client_uuid' => 'uuid-1',
                'op_type' => 'create_event',
                'payload' => [
                    'title' => 'Test Event',
                    'description' => 'Test',
                    'latitude' => 40.7128,
                    'longitude' => -74.0060,
                ],
            ],
        ],
    ]);

    $response->assertStatus(200)
        ->assertJsonStructure(['results', 'server_time']);

    $result = $response->json('results.0');
    $this->assertEquals('uuid-1', $result['client_uuid']);
    $this->assertEquals('applied', $result['status']);
});

test('sync push is idempotent for same client uuid', function () {
    $user = User::create([
        'username' => 'testuser2',
        'email' => 'test2@example.com',
        'password' => bcrypt('password123'),
        'referral_code' => 'TEST0002',
    ]);

    Sanctum::actingAs($user);

    $payload = [
        'operations' => [
            [
                'client_uuid' => 'uuid-1',
                'op_type' => 'create_event',
                'payload' => [
                    'title' => 'Test Event',
                    'description' => 'Test',
                    'latitude' => 40.7128,
                    'longitude' => -74.0060,
                ],
            ],
        ],
    ];

    $this->postJson('/api/sync/push', $payload)->assertStatus(200);
    $this->postJson('/api/sync/push', $payload)->assertStatus(200);

    $this->assertDatabaseCount('events', 1);
});
