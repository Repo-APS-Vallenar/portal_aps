<?php

namespace App\Exports;

use App\Models\AuditLog;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AuditLogsExport implements FromCollection, WithHeadings, WithStyles, WithColumnWidths
{
    public function collection()
    {
        return AuditLog::with('user')->latest()->get()->map(function ($log) {
            return [
                'Fecha'        => $log->created_at->format('d/m/Y H:i'),
                'Usuario'      => $log->user->name ?? 'Sistema',
                'Acción'       => $log->action,
                'Descripción'  => $log->description,
                'IP'           => $log->ip_address,
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Fecha',
            'Usuario',
            'Acción',
            'Descripción',
            'IP',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 18, // Fecha
            'B' => 20, // Usuario
            'C' => 30, // Acción
            'D' => 60, // Descripción
            'E' => 20, // IP
        ];
    }
}
