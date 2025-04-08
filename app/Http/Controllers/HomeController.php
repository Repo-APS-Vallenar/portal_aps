<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        // Datos de ejemplo para las plataformas
        $platforms = [
            'clinicos' => [
                [
                    'nombre' => 'IZY TIMECONTROL',
                    'descripcion' => 'AutoConsultaSistema de Control de Asistencia',
                    'url' => 'https://deptodesalud.izytimecontrol.com/#/auto-consulta/login',
                    'icono' => '',
                    'imagen' => 'izy-timecontrol.png'
                ],
                [
                    'nombre' => 'RAYEN APS',
                    'descripcion' => 'Accede a una mejor experiencia para la APS Digital',
                    'url' => 'https://www.rayenaps.cl/',
                    'icono' => '',
                    'imagen' => 'rayen-aps.png'
                ],
                [
                    'nombre' => 'IMED (Usar lector de huellas)',
                    'descripcion' => 'Licencia medica electronica',
                    'url' => 'https://www.licencia.cl/sesiones/nueva/rol.profesional',
                    'icono' => '',
                    'imagen' => 'imed.png'
                ],
            ],
        ];

        return view('home', compact('platforms'));
    }
}
