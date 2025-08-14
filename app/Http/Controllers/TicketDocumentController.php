<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\TicketDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use App\Services\SecurityLogger;

class TicketDocumentController extends Controller
{
    public function store(Request $request, Ticket $ticket)
    {
        // Verificar permisos - solo el creador, asignado o admins pueden subir documentos
        $user = Auth::user();
        if (!$user) {
            abort(401, 'No autenticado');
        }

        $canUpload = $user->role === 'admin' || 
                    $user->role === 'superadmin' || 
                    $ticket->created_by === $user->id || 
                    $ticket->assigned_to === $user->id;

        if (!$canUpload) {
            SecurityLogger::logUnauthorizedAccess(
                "ticket_document_upload:ticket_{$ticket->id}",
                'upload'
            );
            abort(403, 'No tienes permisos para subir documentos a este ticket');
        }

        $request->validate([
            'document' => [
                'required',
                'file',
                'max:10240', // Máximo 10MB
                'mimes:jpeg,jpg,png,pdf,doc,docx,txt,xlsx,xls', // Tipos permitidos
                function ($attribute, $value, $fail) {
                    // Verificar contenido real del archivo
                    $finfo = finfo_open(FILEINFO_MIME_TYPE);
                    $mimeType = finfo_file($finfo, $value->getPathname());
                    finfo_close($finfo);
                    
                    $allowedMimes = [
                        'image/jpeg', 'image/jpg', 'image/png',
                        'application/pdf',
                        'application/msword',
                        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                        'text/plain',
                        'application/vnd.ms-excel',
                        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
                    ];
                    
                    if (!in_array($mimeType, $allowedMimes)) {
                        $fail('El archivo no es de un tipo permitido.');
                    }
                }
            ],
            'description' => 'nullable|string|max:255'
        ], [
            'document.required' => 'Debe seleccionar un archivo.',
            'document.max' => 'El archivo no puede ser mayor a 10MB.',
            'document.mimes' => 'Solo se permiten archivos: JPEG, PNG, PDF, DOC, DOCX, TXT, XLS, XLSX.',
            'description.max' => 'La descripción no puede tener más de 255 caracteres.'
        ]);

        $file = $request->file('document');
        
        // Sanitizar nombre del archivo
        $originalName = $file->getClientOriginalName();
        $sanitizedName = preg_replace('/[^a-zA-Z0-9._-]/', '_', $originalName);
        $sanitizedName = substr($sanitizedName, 0, 100); // Limitar longitud
        
        // Generar nombre único para evitar colisiones
        $uniqueName = time() . '_' . $sanitizedName;
        
        $path = $file->storeAs('ticket-documents/' . $ticket->id, $uniqueName, 'public');

        $document = TicketDocument::create([
            'ticket_id' => $ticket->id,
            'user_id' => Auth::id(),
            'file_name' => $file->getClientOriginalName(),
            'file_path' => $path,
            'file_type' => $file->getClientMimeType(),
            'file_size' => $file->getSize(),
            'description' => $request->description
        ]);
        
        // Log del upload de archivo
        SecurityLogger::logFileUpload(
            $file->getClientOriginalName(),
            $file->getClientMimeType(),
            $file->getSize(),
            "ticket_{$ticket->id}"
        );

        // Emitir evento broadcast
        $documentData = $document->toArray();
        $documentData['user'] = [
            'name' => $document->user->name ?? 'Desconocido',
        ];
        $documentData['created_at'] = $document->created_at ? $document->created_at->format('d/m/Y H:i') : '';
        event(new \App\Events\DocumentAdded($ticket->id, $documentData));

        // Notificar al creador y asignado si no son quienes subieron el archivo
        $usuariosNotificar = collect();
        if ($ticket->creator && $ticket->creator->id !== Auth::id()) {
            $usuariosNotificar->push($ticket->creator);
        }
        if ($ticket->assignedTo && $ticket->assignedTo->id !== Auth::id()) {
            $usuariosNotificar->push($ticket->assignedTo);
        }
        // Notificar a todos los superadmins
        $superadmins = \App\Models\User::where('role', 'superadmin')->get();
        $usuariosNotificar = $usuariosNotificar->merge($superadmins);
        // Notificar a todos los admins
        $admins = \App\Models\User::where('role', 'admin')->get();
        $usuariosNotificar = $usuariosNotificar->merge($admins);
        $usuariosNotificar = $usuariosNotificar->unique('id');
        foreach ($usuariosNotificar as $usuario) {
            $usuario->notify(new \App\Notifications\TicketAttachmentAddedNotification(
                $ticket,
                $file->getClientOriginalName(),
                Auth::user(),
                $usuario->id
            ));
        }

        // Registrar en auditoría
        app(\App\Http\Controllers\TicketController::class)->logAudit('Subir Documento', 'Documento "' . $file->getClientOriginalName() . '" subido por: ' . Auth::user()->name . ' al ticket #' . $ticket->id);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'document' => $document,
                'message' => 'Documento subido exitosamente'
            ]);
        }

        return redirect()->route('tickets.show', $ticket->id)->with('success', '¡Documento subido correctamente!');
    }

    public function destroy(TicketDocument $document)
    {
        $this->authorize('delete', $document->ticket);

        Storage::disk('public')->delete($document->file_path);
        $document->delete();

        // Emitir evento broadcast
        event(new \App\Events\DocumentDeleted($document->ticket_id, $document->id));

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Documento eliminado exitosamente'
            ]);
        }

        return redirect()->back()->with('success', 'Documento eliminado exitosamente');
    }

    public function download(TicketDocument $document)
    {
        $this->authorize('view', $document->ticket);

        $path = $document->file_path;
        $disk = Storage::disk('public');

        if (!$disk->exists($path)) {
            abort(404, 'Archivo no encontrado');
        }

        return $disk->download($path, $document->file_name);
    }
} 