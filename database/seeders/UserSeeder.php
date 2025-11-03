<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Administrador',
            'email' => 'admin@test.com',
            'password' => Hash::make('admin123'),
            'role_id' => 1,
        ]);

        User::create([
            'name' => 'Usuario Demo',
            'email' => 'user@test.com',
            'password' => Hash::make('user123'),
            'role_id' => 2,
        ]);

        User::create([
            'name' => 'Usuario Test',
            'email' => 'test@test.com',
            'password' => Hash::make('test123'),
            'role_id' => 2,
        ]);
    }
}
