<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Location;

class LocationSeeder extends Seeder
{
    public function run()
    {
        $ubicaciones = [
            'Baquedano',
            'Carrera',
            'Joan Crawford',
            'Estación',
            'Depto. de Salud',
            'Cachiyuyo',
            'Domeyko',
            'Incahuasi',
            'Hacienda Ventana'
        ];

        foreach ($ubicaciones as $ubicacion) {
            \App\Models\Location::updateOrCreate(
                ['name' => $ubicacion],
                ['name' => $ubicacion]
            );
        }
    }
}
