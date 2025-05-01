<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        
        // Usamos solo AdminUserSeeder que ya contiene los usuarios admin necesarios
        $this->call(AdminUserSeeder::class);
        // No usamos UserSeeder para evitar conflictos de correos duplicados
        // $this->call(UserSeeder::class);
    }
}
