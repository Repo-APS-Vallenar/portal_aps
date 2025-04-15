<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AuditLog;
class AuditLogController extends Controller
{
    public function index()
    {
        if (auth()->user()->role !== 'superadmin') {
            return redirect()->route('home')->with('error', 'No tienes permiso para acceder a la auditoría.');
        }

        $logs = AuditLog::with('user')->latest()->paginate(20);
        return view('audit.index', compact('logs'));
    }
}