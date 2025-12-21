<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleAndPermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()['cache']->forget('spatie.permission.cache');

        // Crear roles
        $superAdmin = Role::create(['name' => 'SuperAdmin']);
        $candidato = Role::create(['name' => 'Candidato']);
        $lider = Role::create(['name' => 'Lider']);
        $usuarioBasico = Role::create(['name' => 'Usuario Basico']);

        // Permisos basicos (puedes expandir)
        $permissions = [
            'view-users',
            'manage-users',
            'manage-roles',
            'manage-modules',
            'manage-banners',
            'manage-events',
            'manage-notifications',
            'manage-settings',
            'view-analytics',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Asignar permisos a roles
        $superAdmin->givePermissionTo(Permission::all());

        $candidato->givePermissionTo([
            'view-users',
            'view-analytics',
        ]);

        $lider->givePermissionTo([
            'view-users',
            'manage-events',
            'view-analytics',
        ]);

        $usuarioBasico->givePermissionTo([
            'view-users',
        ]);
    }
}
