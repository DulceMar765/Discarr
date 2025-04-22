<?php
// filepath: c:\Users\onlyc\WebDev_Project\Discarr\database\seeders\AdminUserSeeder.php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class AdminUserSeeder extends Seeder
{
    public function run()
    {
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'role' => 'admin', // Asegúrate de que el campo "role" exista en tu tabla "users"
        ]);
    }
}
