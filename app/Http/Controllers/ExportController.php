<?php

namespace App\Http\Controllers;

use App\Exports\AuditLogsExport;
use Maatwebsite\Excel\Facades\Excel;

class ExportController extends Controller
{
    public function exportAuditLogs()
    {
        $logs = $this->getAuditLogs(); // Replace with the actual method or logic to retrieve $logs
        return Excel::download(new AuditLogsExport($logs), 'bitacora_auditoria__'.now()->format('Y-m-d_H:i:s') . '.xlsx');
    }
}
