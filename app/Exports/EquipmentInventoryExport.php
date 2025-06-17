<?php

namespace App\Exports;

use App\Models\EquipmentInventory;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class EquipmentInventoryExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    public function collection()
    {
        return EquipmentInventory::with('location')->get();
    }

    public function headings(): array
    {
        return [
            'MARCA',
            'MODELO',
            'NUMERO SERIE',
            'UBICACIÓN',
            'USUARIO',
            'BOX/OFICINA',
            'IP/RED WIFI',
            'CPU',
            'RAM',
            'CAPACIDAD ALMACENAMIENTO',
            'TARJETA DE VIDEO',
            'ID ANYDESK',
            'PASS AnyDesk',
            'VERSION DE WINDOWS',
            'LICENCIA WINDOWS',
            'VERSION OFFICE',
            'LICENCIA OFFICE',
            'PASSWORD CUENTA',
            'FECHA INSTALACION',
            'COMENTARIOS'
        ];
    }

    public function map($equipment): array
    {
        return [
            $equipment->marca,
            $equipment->modelo,
            $equipment->numero_serie,
            $equipment->location->name ?? 'N/A',
            $equipment->usuario,
            $equipment->box_oficina,
            $equipment->ip_red_wifi,
            $equipment->cpu,
            $equipment->ram,
            $equipment->capacidad_almacenamiento,
            $equipment->tarjeta_video,
            $equipment->id_anydesk,
            $equipment->pass_anydesk,
            $equipment->version_windows,
            $equipment->licencia_windows,
            $equipment->version_office,
            $equipment->licencia_office,
            $equipment->password_cuenta,
            $equipment->fecha_instalacion ? $equipment->fecha_instalacion->format('d/m/Y') : '',
            $equipment->comentarios
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Estilo para la primera fila (encabezados)
            1    => [
                'font' => ['bold' => true],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'color' => ['argb' => 'FFE0E0E0'], // Un gris claro para el fondo
                ],
            ],
            // Estilo para todas las celdas: añadir bordes
            'A1:T' . $sheet->getHighestRow() => [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => ['argb' => 'FF000000'],
                    ],
                ],
            ],
        ];
    }
} 