<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TicketStatus;

class StatusSeeder extends Seeder
{
    public function run()
    {
        $statuses = [
            ['name' => 'Solicitado', 'color' => '#01a3d5'],     // Rojo
            ['name' => 'Pendiente', 'color' => '#dc3545'],     // Rojo
            ['name' => 'En Proceso', 'color' => '#ffc107'],    // Amarillo
            ['name' => 'Resuelto', 'color' => '#28a745'],      // Verde
            ['name' => 'Cerrado', 'color' => '#6c757d'],       // Gris
        ];

        foreach ($statuses as $status) {
            \App\Models\TicketStatus::updateOrCreate(
                ['name' => $status['name']],
                $status
            );
        }
    }
}
