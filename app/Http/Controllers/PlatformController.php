<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PlatformController extends Controller
{
    public function index()
    {
        // Datos de ejemplo para las plataformas
        $platforms = [
            'salud' => [
                [
                    'nombre' => 'IZY TIMECONTROL',
                    'descripcion' => 'Sistema de Control de Asistencia',
                    'url' => 'https://deptodesalud.izytimecontrol.com/#/auto-consulta/login',
                    'imagen' => 'izy-timecontrol.png',
                    'categoria' => 'Control de Asistencia',
                    'estadisticas' => [
                        'usuarios_activos' => 150,
                        'accesos_diarios' => 45,
                        'ultima_actualizacion' => '2024-03-15'
                    ],
                    'guia_acceso' => 'Para acceder, utilice su RUT y contraseña institucional.',
                    'contacto_soporte' => 'soporte@izytimecontrol.com'
                ],
                [
                    'nombre' => 'RAYEN APS',
                    'descripcion' => 'Accede a una mejor experiencia para la APS Digital',
                    'url' => 'https://www.rayenaps.cl/',
                    'imagen' => 'rayen-aps.png',
                    'categoria' => 'Gestión APS',
                    'estadisticas' => [
                        'usuarios_activos' => 200,
                        'accesos_diarios' => 75,
                        'ultima_actualizacion' => '2024-03-10'
                    ],
                    'guia_acceso' => 'Acceso mediante certificado digital o credenciales institucionales.',
                    'contacto_soporte' => 'soporte@rayenaps.cl'
                ],
                [
                    'nombre' => 'IMED',
                    'descripcion' => 'Licencia médica electrónica',
                    'url' => 'https://www.licencia.cl/sesiones/nueva/rol.profesional',
                    'imagen' => 'imed.png',
                    'categoria' => 'Licencias Médicas',
                    'estadisticas' => [
                        'usuarios_activos' => 80,
                        'accesos_diarios' => 30,
                        'ultima_actualizacion' => '2024-03-01'
                    ],
                    'guia_acceso' => 'Se requiere lector de huellas y credenciales MINSAL.',
                    'contacto_soporte' => 'soporte@imed.cl'
                ],
            ],
        ];

        return view('platforms.index', compact('platforms'));
    }
} 