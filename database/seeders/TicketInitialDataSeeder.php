<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\TicketCategory;
use App\Models\TicketStatus;

class TicketInitialDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear estados de tickets
        $statuses = [
            ['name' => 'Pendiente', 'description' => 'Ticket recién creado', 'color' => '#FFD700', 'is_active' => true],
            ['name' => 'En Proceso', 'description' => 'Ticket en proceso de resolución', 'color' => '#FFA500', 'is_active' => true],
            ['name' => 'Resuelto', 'description' => 'Ticket resuelto', 'color' => '#90EE90', 'is_active' => true],
            ['name' => 'Cerrado', 'description' => 'Ticket cerrado', 'color' => '#FFB6C1', 'is_active' => true],
            ['name' => 'Cancelado', 'description' => 'Ticket cancelado', 'color' => '#DEB887', 'is_active' => true],
        ];

        foreach ($statuses as $status) {
            \App\Models\TicketStatus::updateOrCreate(
                ['name' => $status['name']],
                $status
            );
        }

        // Crear categorías de tickets
        $categories = [
            ['name' => 'Hardware', 'description' => 'Problemas con equipos físicos', 'is_active' => true],
            ['name' => 'Software', 'description' => 'Problemas con programas y aplicaciones', 'is_active' => true],
            ['name' => 'Red', 'description' => 'Problemas de conectividad y red', 'is_active' => true],
            ['name' => 'Usuario', 'description' => 'Solicitudes de usuarios', 'is_active' => true],
            ['name' => 'Otro', 'description' => 'Otras solicitudes', 'is_active' => true],
        ];

        foreach ($categories as $category) {
            \App\Models\TicketCategory::updateOrCreate(
                ['name' => $category['name']],
                $category
            );
        }
    }
}
