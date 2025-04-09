<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
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
        if (!User::where('email', 'admin@aps.com')->exists()) {
            User::create([
                'name' => 'Administrador',
                'email' => 'admin@aps.com',
                'password' => Hash::make('admin123'),
                'role' => 'admin' // si tienes esta columna
            ]);
        }
    }
}
