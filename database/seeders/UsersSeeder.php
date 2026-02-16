<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UsersSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::create([
            'id' => (string) Str::uuid(),
            'first_name' => 'CIPMA',
            'last_name' => 'Admin',
            'email' => 'admin@cipma-irobotics.com',
            'password' => Hash::make('admin@cipma.2025'),
            'email_verified_at' => now(),
            'is_active' => true,
            'must_change_password' => true,
        ]);

        $admin->assignRole('super_admin');
        
        $comptable = User::create([
            'id' => (string) Str::uuid(),
            'first_name' => 'CIPMA',
            'last_name' => 'Comptable',
            'email' => 'comptable@cipma-irobotics.com',
            'password' => Hash::make('comptable@cipma.2025'),
            'email_verified_at' => now(),
            'is_active' => true,
            'must_change_password' => true,
        ]);

        $comptable->assignRole('comptabilite');
       
        $formateur = User::create([
            'id' => (string) Str::uuid(),
            'first_name' => 'CIPMA',
            'last_name' => 'Formateur',
            'email' => 'formateur@cipma-irobotics.com',
            'password' => Hash::make('formateur@cipma.2025'),
            'email_verified_at' => now(),
            'is_active' => true,
            'must_change_password' => true,
        ]);

        $formateur->assignRole('formateur');

        $responsable = User::create([
            'id' => (string) Str::uuid(),
            'first_name' => 'CIPMA',
            'last_name' => 'Responsable Académique',
            'email' => 'responsable@cipma-irobotics.com',
            'password' => Hash::make('responsable@cipma.2025'),
            'email_verified_at' => now(),
            'is_active' => true,
            'must_change_password' => true,
        ]);

        $responsable->assignRole('responsable_academique');

        $it = User::create([
            'id' => (string) Str::uuid(),
            'first_name' => 'CIPMA',
            'last_name' => 'IT',
            'email' => 'it@cipma-irobotics.com',
            'password' => Hash::make('it@cipma.2025'),
            'email_verified_at' => now(),
            'is_active' => true,
            'must_change_password' => true,
        ]);

        $it->assignRole('it');
       
    }
}