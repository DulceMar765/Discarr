<?php
// filepath: c:\Users\onlyc\WebDev_Project\Discarr\database\seeders\AdminSeeder.php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class AdminSeeder extends Seeder
{
    public function run()
    {
        User::create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'), // Cambia "password" por la contraseña que desees
            'role' => 'admin',
        ]);
        User::create([
            'name' => 'Bryan Diaz',
            'email' => 'bryandiaz1810@gmail.com',
            'password' => bcrypt('istroudgamer123'),
            'role' => 'admin',
        ]);
    }
}
