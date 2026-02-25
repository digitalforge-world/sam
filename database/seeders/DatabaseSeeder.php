<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Créer les rôles et permissions d'abord
        $this->call(RolesAndPermissionsSeeder::class);

        // 2. Créer les utilisateurs par défaut avec leurs rôles
        $this->call(DefaultUsersSeeder::class);
    }
}
