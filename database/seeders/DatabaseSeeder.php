<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Création du compte Administrateur (La tresseuse)
        User::updateOrCreate(
            ['email' => 'tambon.djebarlen@gmail.com'],
            [
                'name' => 'Djebarlen TAMBON',
                'phone' => '06 92 00 00 00',
                'password' => Hash::make('password'), // Le mot de passe sera : password
                'role' => 'admin',
            ]
        );

        // Optionnel : Création d'un compte client pour tes tests
        User::updateOrCreate(
            ['email' => 'tambon.djebarlen@gmail.com'],
            [
                'name' => 'Djebarlen TAMBON',
                'phone' => '06 93 05 70 66',
                'password' => Hash::make('password'),
                'role' => 'client',
            ]
        );
    }
}
