<?php

namespace App\Http\Controllers;

use App\Exports\AuditLogsExport;
use Maatwebsite\Excel\Facades\Excel;

class ExportController extends Controller
{
    public function exportAuditLogs()
    {
        return Excel::download(new AuditLogsExport, 'bitacora_auditoria.xlsx');
    }
}
