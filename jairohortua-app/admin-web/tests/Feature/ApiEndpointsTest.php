<?php

use App\Models\Event;
use App\Models\Referral;
use App\Models\Setting;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Role;

test('event attend registers attendance', function () {
    $user = User::create([
        'username' => 'attender',
        'email' => 'attender@example.com',
        'password' => bcrypt('password123'),
        'referral_code' => 'ATT0001',
    ]);

    $event = Event::create([
        'title' => 'Event',
        'description' => 'Test',
        'latitude' => 1.0,
        'longitude' => 2.0,
        'starts_at' => now(),
        'created_by' => $user->id,
    ]);

    Sanctum::actingAs($user);

    $response = $this->postJson("/api/events/{$event->id}/attend");
    $response->assertStatus(201);

    $this->assertDatabaseHas('event_attendances', [
        'event_id' => $event->id,
        'user_id' => $user->id,
    ]);
});

test('referral use-code creates referral', function () {
    $referrer = User::create([
        'username' => 'referrer',
        'email' => 'referrer@example.com',
        'password' => bcrypt('password123'),
        'referral_code' => 'REF0001',
    ]);

    $user = User::create([
        'username' => 'invitee',
        'email' => 'invitee@example.com',
        'password' => bcrypt('password123'),
        'referral_code' => 'INV0001',
    ]);

    Sanctum::actingAs($user);

    $response = $this->postJson('/api/referrals/use-code', [
        'code' => $referrer->referral_code,
    ]);

    $response->assertStatus(201);
    $this->assertDatabaseHas('referrals', [
        'referrer_id' => $referrer->id,
        'referred_id' => $user->id,
    ]);
});

test('settings app returns config keys', function () {
    Setting::set('social_facebook_url', 'https://facebook.com');
    Setting::set('social_instagram_url', 'https://instagram.com');

    $user = User::create([
        'username' => 'settings',
        'email' => 'settings@example.com',
        'password' => bcrypt('password123'),
        'referral_code' => 'SET0001',
    ]);

    Sanctum::actingAs($user);

    $this->getJson('/api/settings/app')
        ->assertStatus(200)
        ->assertJsonStructure([
            'social_facebook_url',
            'social_instagram_url',
            'notification_radius_km',
            'notification_days_window',
            'app_name',
            'app_version',
        ]);
});

test('device tokens can be stored', function () {
    $user = User::create([
        'username' => 'deviceuser',
        'email' => 'device@example.com',
        'password' => bcrypt('password123'),
        'referral_code' => 'DEV0001',
    ]);

    Sanctum::actingAs($user);

    $response = $this->postJson('/api/device-tokens', [
        'token' => 'token-123',
        'platform' => 'android',
    ]);

    $response->assertStatus(201);
    $this->assertDatabaseHas('device_tokens', [
        'user_id' => $user->id,
        'token' => 'token-123',
    ]);
});

test('referrals by id require superadmin', function () {
    Role::create(['name' => 'SuperAdmin', 'guard_name' => 'web']);
    Role::create(['name' => 'Candidato', 'guard_name' => 'web']);

    $admin = User::create([
        'username' => 'adminuser',
        'email' => 'admin@example.com',
        'password' => bcrypt('password123'),
        'referral_code' => 'ADM0001',
    ]);
    $admin->assignRole('SuperAdmin');

    $user = User::create([
        'username' => 'normal',
        'email' => 'normal@example.com',
        'password' => bcrypt('password123'),
        'referral_code' => 'NOR0001',
    ]);
    $user->assignRole('Candidato');

    Sanctum::actingAs($user);
    $this->getJson("/api/users/{$admin->id}/referrals")->assertStatus(403);

    Sanctum::actingAs($admin);
    $this->getJson("/api/users/{$user->id}/referrals")->assertStatus(200);
});
