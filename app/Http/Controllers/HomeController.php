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
                    'descripcion' => 'AutoConsulta | Sistema de Control de Asistencia',
                    'url' => 'https://deptodesalud.izytimecontrol.com/#/auto-consulta/login',
                    'icono' => 'fa-clock',
                    'imagen' => 'izy-timecontrol.png'
                ],
                [
                    'nombre' => 'RAYEN APS',
                    'descripcion' => 'Accede a una mejor experiencia para la APS Digital',
                    'url' => 'https://www.rayenaps.cl/',
                    'icono' => 'fa-hospital',
                    'imagen' => 'rayen-aps.png'
                ],
                [
                    'nombre' => 'IMED',
                    'descripcion' => 'Licencia medica electronica | ¡Usar lector de huellas!',
                    'url' => 'https://www.licencia.cl/sesiones/nueva/rol.profesional',
                    'icono' => 'fa-file-medical',
                    'imagen' => 'imed.png'
                ],
                [
                    'nombre' => 'TicketGo',
                    'descripcion' => 'Sistema de tickets para reportes técnicos en centros de salud.',
                    'url' => '/tickets',
                    'icono' => 'fa-ticket-alt',
                    'imagen' => 'fixsalud.png'
                ],
                [
                    'nombre' => 'HoraFacil',
                    'descripcion' => 'Sistema de Gestión de Horarios',
                    'url' => 'https://vallenar.horafacil.cl/login',
                    'icono' => 'fa-calendar-alt',
                    'imagen' => 'horafacil.png'
                ],
                [
                    'nombre' => 'Carrera Funcionaria',
                    'descripcion' => 'Sistema de Gestión de Carrera Funcionaria',
                    'url' => 'https://vallenar.carrerafuncionaria.com/login',
                    'icono' => 'fa-users',
                    'imagen' => 'carrera-funcionaria.png'
                ],
            ],
        ];

        return view('home', compact('platforms'));
    }
}
