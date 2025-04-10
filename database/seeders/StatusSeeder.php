<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TicketStatus;

class StatusSeeder extends Seeder
{
    public function run()
    {
        TicketStatus::truncate(); // Limpia la tabla antes de poblarla

        TicketStatus::insert([
            ['name' => 'Pendiente', 'color' => '#dc3545'],     // Rojo
            ['name' => 'En Proceso', 'color' => '#ffc107'],    // Amarillo
            ['name' => 'Resuelto', 'color' => '#28a745'],      // Verde
            ['name' => 'Cerrado', 'color' => '#6c757d'],       // Gris
        ]);
    }
}
