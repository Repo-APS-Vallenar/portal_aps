<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AuditLogsExport implements FromArray, WithHeadings, WithStyles
{
    protected $logs;

    public function __construct(array $logs)
    {
        $this->logs = $logs;
    }
    public function styles(Worksheet $sheet)
    {
        // Encabezado estilizado
        $sheet->getStyle('A1:E1')->applyFromArray([
            'font' => ['bold' => true],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FFD9EDF7'],
            ],
        ]);

        // Ancho personalizado para cada columna
        $sheet->getColumnDimension('A')->setWidth(16); // Fecha
        $sheet->getColumnDimension('B')->setWidth(14); // Usuario
        $sheet->getColumnDimension('C')->setWidth(22); // Acción
        $sheet->getColumnDimension('D')->setWidth(55); // Descripción
        $sheet->getColumnDimension('E')->setWidth(12); // IP

        // Altura de filas para mejor visibilidad
        $highestRow = $sheet->getHighestRow();
        for ($row = 1; $row <= $highestRow; $row++) {
            $sheet->getRowDimension($row)->setRowHeight(15);
        }

        return [];
    }


    public function array(): array
    {
        return $this->logs;
    }

    public function headings(): array
    {
        return ['Fecha', 'Usuario', 'Acción', 'Descripción', 'IP'];
    }

}
