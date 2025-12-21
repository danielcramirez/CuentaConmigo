<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\EventController;
use App\Http\Controllers\Api\LocationController;
use App\Http\Controllers\Api\BannerController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\SyncController;
use App\Http\Controllers\Api\ReferralController;
use App\Http\Controllers\Api\SettingController;
use App\Http\Controllers\Api\DeviceTokenController;

// Rutas publicas (sin autenticacion)
Route::prefix('auth')->group(function () {
    Route::post('login', [AuthController::class, 'login'])->name('auth.login');
});

// Rutas protegidas (requieren token)
Route::middleware('auth:sanctum')->group(function () {
    // Auth
    Route::prefix('auth')->group(function () {
        Route::post('refresh', [AuthController::class, 'refresh'])->name('auth.refresh');
        Route::post('logout', [AuthController::class, 'logout'])->name('auth.logout');
    });

    // Users
    Route::prefix('users')->group(function () {
        Route::get('me', [UserController::class, 'me'])->name('users.me');
        Route::get('me/dashboard', [UserController::class, 'dashboard'])->name('users.dashboard');
        Route::get('referrals', [UserController::class, 'referrals'])->name('users.referrals');
        Route::get('{id}/referrals', [UserController::class, 'referrals'])->name('users.referrals.by_id');
    });

    // Events
    Route::prefix('events')->group(function () {
        Route::get('/', [EventController::class, 'index'])->name('events.index');
        Route::get('{event}', [EventController::class, 'show'])->name('events.show');
        Route::post('{event}/attend', [EventController::class, 'attend'])->name('events.attend');
    });

    // Location
    Route::prefix('location')->group(function () {
        Route::post('/', [LocationController::class, 'store'])->name('location.store');
    });

    // Banners
    Route::prefix('banners')->group(function () {
        Route::get('active', [BannerController::class, 'active'])->name('banners.active');
    });

    // Notifications
    Route::prefix('notifications')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('notifications.index');
        Route::post('{notification}/read', [NotificationController::class, 'read'])->name('notifications.read');
    });

    // Sync
    Route::prefix('sync')->group(function () {
        Route::post('push', [SyncController::class, 'push'])->name('sync.push');
        Route::get('pull', [SyncController::class, 'pull'])->name('sync.pull');
    });

    // Referrals
    Route::prefix('referrals')->group(function () {
        Route::post('use-code', [ReferralController::class, 'useCode'])->name('referrals.use_code');
        Route::get('my-stats', [ReferralController::class, 'myStats'])->name('referrals.my_stats');
    });

    // Settings
    Route::prefix('settings')->group(function () {
        Route::get('app', [SettingController::class, 'app'])->name('settings.app');
    });

    // Device tokens
    Route::post('device-tokens', [DeviceTokenController::class, 'store'])->name('device_tokens.store');
});
