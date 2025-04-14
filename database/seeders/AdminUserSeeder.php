<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@aps.com'],
            [
                'name' => 'Administrador',
                'password' => Hash::make('admin123'),
                'role' => 'superadmin' // solo si tienes esta columna en la base de datos
            ]
        );
        User::updateOrCreate(
            ['email' => 'superadmin@aps.com'],
            [
                'name' => 'Super Admin',
                'password' => bcrypt('12345678'),
                'role' => 'superadmin',
            ]
        );
    }
}
