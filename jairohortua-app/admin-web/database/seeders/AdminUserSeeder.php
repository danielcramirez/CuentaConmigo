<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        // Crear usuario admin
        $admin = User::create([
            'username' => 'admin',
            'email' => 'admin@jairohortua.com',
            'password' => Hash::make('admin123'), // CAMBIAR EN PRODUCCION
            'referral_code' => 'ADMIN0001',
        ]);

        $admin->assignRole('SuperAdmin');

        // Crear usuario de prueba: candidato
        $candidato = User::create([
            'username' => 'candidato1',
            'email' => 'candidato@example.com',
            'password' => Hash::make('password123'),
            'referrer_id' => $admin->id,
        ]);
        $candidato->assignRole('Candidato');
        $candidato->generateReferralCode();

        // Crear usuario de prueba: lider
        $lider = User::create([
            'username' => 'lider1',
            'email' => 'lider@example.com',
            'password' => Hash::make('password123'),
            'referrer_id' => $admin->id,
        ]);
        $lider->assignRole('Lider');
        $lider->generateReferralCode();
    }
}
