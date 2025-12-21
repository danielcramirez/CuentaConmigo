<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Admin\EventController as AdminEventController;
use App\Http\Controllers\Admin\BannerController as AdminBannerController;
use App\Http\Controllers\Admin\SettingController as AdminSettingController;
use App\Http\Controllers\Admin\ReferralController as AdminReferralController;
use App\Http\Controllers\Admin\ModuleController as AdminModuleController;
use App\Http\Controllers\Admin\RoleController as AdminRoleController;

Route::prefix('admin')->middleware(['auth', 'role:SuperAdmin'])->group(function () {
    Route::get('/', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/stats', [AdminController::class, 'stats'])->name('admin.stats');

    // Users
    Route::get('/users', [UserManagementController::class, 'index'])->name('admin.users.index');
    Route::get('/users/create', [UserManagementController::class, 'create'])->name('admin.users.create');
    Route::post('/users', [UserManagementController::class, 'store'])->name('admin.users.store');
    Route::get('/users/{user}/edit', [UserManagementController::class, 'edit'])->name('admin.users.edit');
    Route::put('/users/{user}', [UserManagementController::class, 'update'])->name('admin.users.update');
    Route::delete('/users/{user}', [UserManagementController::class, 'destroy'])->name('admin.users.destroy');

    // Events
    Route::get('/events', [AdminEventController::class, 'index'])->name('admin.events.index');
    Route::get('/events/create', [AdminEventController::class, 'create'])->name('admin.events.create');
    Route::post('/events', [AdminEventController::class, 'store'])->name('admin.events.store');
    Route::get('/events/{event}/edit', [AdminEventController::class, 'edit'])->name('admin.events.edit');
    Route::put('/events/{event}', [AdminEventController::class, 'update'])->name('admin.events.update');
    Route::delete('/events/{event}', [AdminEventController::class, 'destroy'])->name('admin.events.destroy');

    // Referrals graph
    Route::get('/referrals', [AdminReferralController::class, 'index'])->name('admin.referrals.index');
    Route::get('/referrals/graph-data', [AdminReferralController::class, 'graphData'])->name('admin.referrals.graph');

    // Banners
    Route::get('/banners', [AdminBannerController::class, 'index'])->name('admin.banners.index');
    Route::get('/banners/create', [AdminBannerController::class, 'create'])->name('admin.banners.create');
    Route::post('/banners', [AdminBannerController::class, 'store'])->name('admin.banners.store');
    Route::get('/banners/{banner}/edit', [AdminBannerController::class, 'edit'])->name('admin.banners.edit');
    Route::put('/banners/{banner}', [AdminBannerController::class, 'update'])->name('admin.banners.update');
    Route::delete('/banners/{banner}', [AdminBannerController::class, 'destroy'])->name('admin.banners.destroy');

    // Settings
    Route::get('/settings', [AdminSettingController::class, 'index'])->name('admin.settings.index');
    Route::put('/settings', [AdminSettingController::class, 'update'])->name('admin.settings.update');

    // Modules
    Route::get('/modules', [AdminModuleController::class, 'index'])->name('admin.modules.index');
    Route::get('/modules/create', [AdminModuleController::class, 'create'])->name('admin.modules.create');
    Route::post('/modules', [AdminModuleController::class, 'store'])->name('admin.modules.store');
    Route::get('/modules/{module}/edit', [AdminModuleController::class, 'edit'])->name('admin.modules.edit');
    Route::put('/modules/{module}', [AdminModuleController::class, 'update'])->name('admin.modules.update');
    Route::delete('/modules/{module}', [AdminModuleController::class, 'destroy'])->name('admin.modules.destroy');

    // Roles
    Route::get('/roles', [AdminRoleController::class, 'index'])->name('admin.roles.index');
    Route::get('/roles/create', [AdminRoleController::class, 'create'])->name('admin.roles.create');
    Route::post('/roles', [AdminRoleController::class, 'store'])->name('admin.roles.store');
    Route::get('/roles/{role}/edit', [AdminRoleController::class, 'edit'])->name('admin.roles.edit');
    Route::put('/roles/{role}', [AdminRoleController::class, 'update'])->name('admin.roles.update');
    Route::delete('/roles/{role}', [AdminRoleController::class, 'destroy'])->name('admin.roles.destroy');
});
