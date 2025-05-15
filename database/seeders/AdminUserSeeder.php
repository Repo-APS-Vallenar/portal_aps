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
            ['email' => 'superadmin@aps.com'],
            [
                'name' => 'Super Admin',
                'password' => bcrypt('superadmin13.7'),
                'role' => 'superadmin',
            ]
        );
    }
}
