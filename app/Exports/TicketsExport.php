<?php

namespace App\Exports;


use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;


class TicketsExport implements FromArray, WithHeadings, ShouldAutoSize, WithStyles
{
    public function styles(Worksheet $sheet)
    {
        // Encabezado
        $sheet->getStyle('A1:H1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FF0070C0'], // azul fuerte
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
        ]);

        // Bordes para toda la tabla
        $highestRow = $sheet->getHighestRow();
        $sheet->getStyle('A1:H' . $highestRow)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => 'FFB0B0B0'],
                ],
            ],
        ]);

        // Fondo alterno para filas de datos
        for ($row = 2; $row <= $highestRow; $row++) {
            if ($row % 2 == 0) {
                $sheet->getStyle('A'.$row.':H'.$row)->applyFromArray([
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => ['argb' => 'FFF2F6FA'], // gris azulado claro
                    ],
                ]);
            }
        }

        // Ajuste de ancho de columnas
        foreach (range('A', 'H') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Altura de filas
        for ($row = 1; $row <= $highestRow; $row++) {
            $sheet->getRowDimension($row)->setRowHeight(20);
        }

        return [];
    }
    protected $tickets;
    protected $dateFrom;
    protected $dateTo;

    public function __construct($tickets, $dateFrom = null, $dateTo = null)
    {
        $this->tickets = $tickets;
        $this->dateFrom = $dateFrom;
        $this->dateTo = $dateTo;
    }

    public function array(): array
    {
        $rows = [];
        // Fila de descripción del rango SOLO si hay filtro de fechas
        if ($this->dateFrom || $this->dateTo) {
            $desc = 'Rango de fechas: ';
            if ($this->dateFrom) {
                $desc .= 'Desde ' . $this->dateFrom . ' ';
            }
            if ($this->dateTo) {
                $desc .= 'Hasta ' . $this->dateTo;
            }
            $rows[] = [$desc];
            $rows[] = [];
        }
        // Datos
        foreach ($this->tickets as $ticket) {
            $rows[] = [
                $ticket->id,
                $ticket->creator ? $ticket->creator->name : '',
                $ticket->category ? $ticket->category->name : '',
                $ticket->status ? $ticket->status->name : '',
                ucfirst($ticket->priority),
                $ticket->title,
                $ticket->assignedTo ? $ticket->assignedTo->name : '',
                $ticket->created_at ? $ticket->created_at->format('Y-m-d H:i') : '',
            ];
        }
        return $rows;
    }

    public function headings(): array
    {
        return [
            'ID',
            'Creado por',
            'Categoría',
            'Estado',
            'Prioridad',
            'Usuario del equipo',
            'Asignado a',
            'Fecha de creación',
        ];
    }
}
