<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AddSolicitadoStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Verificar si el estado "Solicitado" ya existe
        $exists = DB::table('ticket_statuses')->where('name', 'Solicitado')->exists();
        
        if (!$exists) {
            DB::table('ticket_statuses')->insert([
                'name' => 'Solicitado',
                'description' => 'Ticket recién creado por un usuario',
                'color' => '#3498db',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }
}
