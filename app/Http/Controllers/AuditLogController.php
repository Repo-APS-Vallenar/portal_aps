<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\User;
use App\Models\Category;
use App\Models\TicketCategory; // Ensure this is correctly imported
use Illuminate\Support\Facades\Lang;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel as ExcelFacade;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use App\Exports\AuditLogsExport;
use Illuminate\Support\Facades\Auth;

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

        // Asegurarse de que los logs sean una colección de Laravel
        $logs = collect($logs);

        // Transformar los logs usando el método transform
        $logs->transform(function ($log) {
            // Usar la función generarMensajeAuditoria para traducir las acciones
            $log->description = $this->generarMensajeAuditoria($log, [], []);
            return $log;
        });

        $pdf = PDF::loadView('audit.partials.pdf', compact('logs'));
        return $pdf->download('bitacora_' . now()->format('Y-m-d_H:i:s') . '.pdf');
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

        // Asegurarse de que los logs sean una colección de Laravel
        $logs = collect($logs);

        // Transformar los logs usando el método transform
        $logs->transform(function ($log) {
            // Usar la función generarMensajeAuditoria para traducir las acciones
            $log->description = $this->generarMensajeAuditoria($log, [], []);
            return $log;
        });

        $exportData = collect($logs)->map(function ($log) {
            return [
                $log->created_at->format('d/m/Y H:i'),
                $log->user->name ?? 'Sistema',
                $log->action,
                $log->description,
                $log->ip_address,
            ];
        })->toArray();

        return ExcelFacade::download(new AuditLogsExport($exportData), 'bitacora_' . now()->format('Y-m-d_H:i:s') . '.xlsx');
    }

    public function exportSelected(Request $request)
    {
        $ids = $request->input('selected_logs', []);

        $logs = AuditLog::with('user')->whereIn('id', $ids)->get();

        // Asegurarse de que los logs sean una colección de Laravel
        $logs = collect($logs);

        // Transformar los logs usando el método transform
        $logs->transform(function ($log) {
            // Usar la función generarMensajeAuditoria para traducir las acciones
            $log->description = $this->generarMensajeAuditoria($log, [], []);
            return $log;
        });

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
    
    /**
     * Genera un mensaje legible para la auditoría.
     * Puedes personalizar la lógica según los campos de AuditLog.
     */
    protected function generarMensajeAuditoria($log, $oldValues = [], $newValues = [])
    {
        // Si ya tienes la descripción, simplemente la devuelves
        if (!empty($log->description)) {
            return $log->description;
        }

        // Si quieres traducir o mejorar el mensaje, hazlo aquí
        return "Acción: {$log->action} realizada por " . ($log->user->name ?? 'Sistema') . " el " . $log->created_at->format('d/m/Y H:i');
    }

    /**
     * Muestra la auditoría de un usuario específico (perfil).
     */
    public function showProfile($userId, Request $request)
    {
        $user = User::findOrFail($userId);
        $query = AuditLog::with('user')->where('user_id', $userId)->latest();

        // Filtros adicionales si se requieren
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }
        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->to);
        }

        $logs = $query->paginate(10);
        $accionesUnicas = AuditLog::select('action')->distinct()->pluck('action');

        return view('audit.profile', compact('user', 'logs', 'accionesUnicas'));
    }

    /**
     * Muestra la auditoría personal del usuario autenticado.
     */
    public function myProfile(Request $request)
    {
        $user = Auth::user();
        $query = AuditLog::with('user')->where('user_id', $user->id)->latest();

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }
        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->to);
        }

        $logs = $query->paginate(10);
        $accionesUnicas = AuditLog::select('action')->distinct()->pluck('action');

        return view('audit.profile', [
            'user' => $user,
            'logs' => $logs,
            'accionesUnicas' => $accionesUnicas
        ]);
    }
}
