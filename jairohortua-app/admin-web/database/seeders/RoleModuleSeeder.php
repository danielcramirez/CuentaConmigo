<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use App\Models\Module;

class RoleModuleSeeder extends Seeder
{
    public function run(): void
    {
        $superAdmin = Role::where('name', 'SuperAdmin')->first();
        $candidato = Role::where('name', 'Candidato')->first();
        $lider = Role::where('name', 'Lider')->first();
        $usuarioBasico = Role::where('name', 'Usuario Basico')->first();

        $allModules = Module::all();
        $basicModules = Module::whereIn('key', ['events', 'notifications', 'referrals'])->get();
        $leaderModules = Module::whereIn('key', ['events', 'notifications', 'referrals'])->get();
        $basicUserModules = Module::whereIn('key', ['notifications', 'referrals'])->get();

        $this->syncRoleModules($superAdmin, $allModules);
        $this->syncRoleModules($candidato, $basicModules);
        $this->syncRoleModules($lider, $leaderModules);
        $this->syncRoleModules($usuarioBasico, $basicUserModules);
    }

    private function syncRoleModules($role, $modules): void
    {
        if (!$role) {
            return;
        }

        DB::table('role_modules')->where('role_id', $role->id)->delete();

        foreach ($modules as $module) {
            DB::table('role_modules')->insert([
                'role_id' => $role->id,
                'module_id' => $module->id,
                'is_visible' => true,
            ]);
        }
    }
}
