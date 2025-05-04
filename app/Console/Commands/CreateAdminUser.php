<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class CreateAdminUser extends Command
{
    protected $signature = 'admin:create {name} {email} {password} {phone} {address}';
    protected $description = 'Crear o actualizar un usuario administrador';

    public function handle()
    {
        $email = $this->argument('email');
        $name = $this->argument('name');
        $password = $this->argument('password');
        $phone = $this->argument('phone');
        $address = $this->argument('address');

        $user = User::where('email', $email)->first();

        if ($user) {
            $user->update([
                'name' => $name,
                'password' => Hash::make($password),
                'role' => 'admin',
                'phone' => $phone,
                'address' => $address
            ]);
            $this->info("Usuario administrador actualizado: {$email}");
        } else {
            User::create([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make($password),
                'role' => 'admin',
                'phone' => $phone,
                'address' => $address
            ]);
            $this->info("Usuario administrador creado: {$email}");
        }

        return 0;
    }
}
