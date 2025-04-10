<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TicketCategory;

class CategorySeeder extends Seeder
{
    public function run()
    {
        TicketCategory::truncate(); // Limpia la tabla antes de poblarla

        TicketCategory::insert([
            ['name' => 'Hardware', 'color' => '#007bff'],     // Azul
            ['name' => 'Software', 'color' => '#28a745'],     // Verde
            ['name' => 'Red', 'color' => '#ffc107'],          // Amarillo
            ['name' => 'Impresoras', 'color' => '#17a2b8'],   // Celeste
            ['name' => 'Otros', 'color' => '#6c757d'],        // Gris
        ]);
    }
}