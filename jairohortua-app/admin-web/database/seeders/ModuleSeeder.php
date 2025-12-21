<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Module;

class ModuleSeeder extends Seeder
{
    public function run(): void
    {
        $modules = [
            ['name' => 'Usuarios', 'key' => 'users', 'description' => 'Gestionar usuarios del sistema', 'icon' => 'users', 'route' => '/admin/users', 'order' => 1],
            ['name' => 'Roles', 'key' => 'roles', 'description' => 'Gestionar roles y permisos', 'icon' => 'shield', 'route' => '/admin/roles', 'order' => 2],
            ['name' => 'Eventos', 'key' => 'events', 'description' => 'Gestionar eventos', 'icon' => 'calendar', 'route' => '/admin/events', 'order' => 3],
            ['name' => 'Banners', 'key' => 'banners', 'description' => 'Gestionar banners publicitarios', 'icon' => 'image', 'route' => '/admin/banners', 'order' => 4],
            ['name' => 'Notificaciones', 'key' => 'notifications', 'description' => 'Centro de notificaciones y historial', 'icon' => 'bell', 'route' => '/admin/notifications', 'order' => 5],
            ['name' => 'Referidos', 'key' => 'referrals', 'description' => 'Ver grafo de referidos', 'icon' => 'share-2', 'route' => '/admin/referrals', 'order' => 6],
            ['name' => 'Configuracion', 'key' => 'settings', 'description' => 'Configuracion global de la app', 'icon' => 'settings', 'route' => '/admin/settings', 'order' => 7],
        ];

        foreach ($modules as $module) {
            Module::updateOrCreate(['key' => $module['key']], $module);
        }
    }
}
