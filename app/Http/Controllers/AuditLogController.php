<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Excel;
use App\Exports\FromCollectionExport;
use Maatwebsite\Excel\Facades\Excel as ExcelFacade;
use PDF;

class AuditLogController extends Controller
{
    public function exportPdf(Request $request)
    {
        $logs = AuditLog::latest()->get();

        $pdf = Pdf::loadView('audit.partials.pdf', compact('logs'));
        return $pdf->download('bitacora.pdf');
    }
    public function index(Request $request)
    {
        $query = AuditLog::with('user')->latest();

        if ($request->has('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->whereHas('user', fn($q2) => $q2->where('name', 'ilike', "%$search%"))
                    ->orWhere('action', 'ilike', "%$search%")
                    ->orWhere('description', 'ilike', "%$search%")
                    ->orWhere('ip_address', 'ilike', "%$search%");
            });
        }

        $logs = $query->simplePaginate(10);

        if ($request->ajax()) {
            return view('audit.partials.logs', compact('logs'))->render();
        }

        return view('audit.index', compact('logs'));
    }

    public function exportExcel()
    {
        $logs = AuditLog::with('user')->latest()->get();

        $exportData = $logs->map(function ($log) {
            return [
                'Fecha' => $log->created_at->format('d/m/Y H:i'),
                'Usuario' => $log->user->name ?? 'Sistema',
                'Acción' => $log->action,
                'Descripción' => $log->description,
                'IP' => $log->ip_address,
            ];
        });

        return ExcelFacade::download(new FromCollectionExport($exportData), 'bitacora.xlsx');
    }


    public function search(Request $request)
    {
        $query = $request->input('query');

        $logs = AuditLog::with('user')
            ->where('description', 'like', "%{$query}%")
            ->orWhere('action', 'like', "%{$query}%")
            ->orWhereHas('user', fn($q) => $q->where('name', 'like', "%{$query}%"))
            ->orderByDesc('created_at')
            ->limit(20)
            ->get();

        return view('audit.partials.logs', compact('logs'));
    }


}
