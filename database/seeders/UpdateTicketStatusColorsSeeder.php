<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UpdateTicketStatusColorsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $statuses = [
            'Solicitado' => '#3498db', // Azul para solicitudes nuevas
            'Pendiente' => '#FFD700',
            'En Proceso' => '#FFA500',
            'Resuelto' => '#90EE90',
            'Cerrado' => '#FFB6C1',
            'Cancelado' => '#DEB887'
        ];

        foreach ($statuses as $name => $color) {
            DB::table('ticket_statuses')
                ->where('name', $name)
                ->update(['color' => $color]);
        }
    }
}
