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
            ['name' => 'Hardware (Falla de computadoras, laptops, etc.)', 'color' => '#007bff'],     // Azul
            ['name' => 'Software (Instalación de software)', 'color' => '#28a745'],     // Verde
            ['name' => 'Red (Configuración de red, wifi, etc.)', 'color' => '#ffc107'],          // Amarillo
            ['name' => 'Impresoras (Configuración de impresoras)', 'color' => '#17a2b8'],   // Celeste
            ['name' => 'Otros (No se puede clasificar en las categorías anteriores)', 'color' => '#6c757d'],        // Gris
        ]);
    }
}