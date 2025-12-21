<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleAndPermissionSeeder::class,
            ModuleSeeder::class,
            SettingSeeder::class,
            RoleModuleSeeder::class,
            AdminUserSeeder::class,
            BannerSeeder::class,
        ]);
    }
}
