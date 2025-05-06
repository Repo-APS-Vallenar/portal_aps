<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Excel;
use App\Exports\FromCollectionExport;
use App\Models\User;
use Maatwebsite\Excel\Facades\Excel as ExcelFacade;
use App\Exports\AuditLogsExport;
use Barryvdh\DomPDF\Facade\Pdf as PDF;

class AuditLogController extends Controller
{
    public function exportPdf(Request $request)
    {
        $query = AuditLog::with('user')->latest();

        if ($request->has('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->whereHas('user', fn($q2) => $q2->whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($search) . '%']))
                    ->orWhereRaw('LOWER(action) LIKE ?', ['%' . strtolower($search) . '%'])
                    ->orWhereRaw('LOWER(description) LIKE ?', ['%' . strtolower($search) . '%'])
                    ->orWhereRaw('LOWER(ip_address) LIKE ?', ['%' . strtolower($search) . '%']);
            });
        }

        $logs = $query->get();

        $pdf = PDF::loadView('audit.partials.pdf', compact('logs'));
        return $pdf->download('bitacora_'.now()->format('Y-m-d_H:i:s') . '.pdf');
    }

    public function index(Request $request)
    {
        $query = AuditLog::with('user')->latest();

        // Filtro de búsqueda general
        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->whereHas('user', fn($q2) => $q2->where('name', 'ilike', "%$search%"))
                    ->orWhere('action', 'ilike', "%$search%")
                    ->orWhere('description', 'ilike', "%$search%")
                    ->orWhere('ip_address', 'ilike', "%$search%");
            });
        }

        // Filtro por usuario
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filtro por acción
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        // Filtro por fechas
        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->from);
        }

        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->to);
        }

        $logs = $query->simplePaginate(7);

        $usuarios = User::orderBy('name')->get();
        $accionesUnicas = AuditLog::select('action')->distinct()->pluck('action');

        if ($request->ajax()) {
            return view('audit.partials.logs', compact('logs'))->render();
        }

        return view('audit.index', compact('logs', 'usuarios', 'accionesUnicas'));
    }


    public function exportExcel(Request $request)
    {
        $query = AuditLog::with('user')->latest();

        if ($request->has('search') && $request->filled('search')) {
            $search = strtolower($request->search);
            $query->where(function ($q) use ($search) {
                $q->whereRaw('LOWER(action) LIKE ?', ["%$search%"])
                    ->orWhereRaw('LOWER(description) LIKE ?', ["%$search%"])
                    ->orWhereRaw('LOWER(ip_address) LIKE ?', ["%$search%"])
                    ->orWhereHas('user', fn($q2) => $q2->whereRaw('LOWER(name) LIKE ?', ["%$search%"]));
            });
        }

        $logs = $query->get();

        $exportData = collect($logs)->map(function ($log) {
            return [
                $log->created_at->format('d/m/Y H:i'),
                $log->user->name ?? 'Sistema',
                $log->action,
                $log->description,
                $log->ip_address,
            ];
        })->toArray();

        return ExcelFacade::download(new AuditLogsExport($exportData), 'bitacora_'.now()->format('Y-m-d_H:i:s') . '.xlsx');
    }

    public function exportSelected(Request $request)
    {
        $ids = $request->input('selected_logs', []);

        $logs = AuditLog::with('user')->whereIn('id', $ids)->get();

        $exportData = collect($logs)->map(function ($log) {
            return [
                $log->created_at->format('d/m/Y H:i'),
                $log->user->name ?? 'Sistema',
                $log->action,
                $log->description,
                $log->ip_address,
            ];
        })->toArray();

        return ExcelFacade::download(new AuditLogsExport($exportData), 'bitacora_seleccionada.xlsx');
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
