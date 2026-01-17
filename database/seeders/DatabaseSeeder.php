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
        $adminPassword = env('ADMIN_PASSWORD', 'ChangeMeAdmin!123');
        $ensemblePassword = env('ENSEMBLE_PASSWORD', 'ensemble123!');

        User::factory()
            ->admin()
            ->create([
                'name' => 'Admin',
                'username' => env('ADMIN_USERNAME', 'admin'),
                'email' => env('ADMIN_EMAIL', 'admin@example.com'),
                'password' => Hash::make($adminPassword),
            ]);

        User::factory()->create([
            'name' => 'Ensemble Login',
            'username' => env('ENSEMBLE_USERNAME', 'ensemble'),
            'email' => env('ENSEMBLE_EMAIL', 'ensemble@example.com'),
            'password' => Hash::make($ensemblePassword),
        ]);

        $this->call(RehearsalSeeder::class);
    }
}
