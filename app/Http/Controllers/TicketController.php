<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\TicketCategory;
use App\Models\TicketStatus;
use App\Models\TicketComment;
use App\Models\User;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\AuditLog;
use App\Mail\TicketCreatedMail;
use App\Mail\TicketUpdatedMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Services\NotificationService;
use App\Notifications\TicketUpdatedNotification;
use App\Notifications\TicketCommentedNotification;

class TicketController extends Controller
{
    /**
     * Constructor del controlador
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    function logAudit($action, $description)
    {
        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => $action,
            'description' => $description,
            'ip_address' => request()->ip(),
        ]);
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();

        if ($user->isAdmin() || $user->isSuperadmin()) {
            $tickets = Ticket::with(['category', 'status', 'creator', 'assignedTo'])
                ->latest()
                ->paginate(10);
        } else {
            $tickets = Ticket::with(['category', 'status', 'creator', 'assignedTo'])
                ->where('created_by', $user->id)
            ->latest()
            ->paginate(10);
        }

        if (request()->ajax()) {
            return response()->view('tickets.partials.tickets-list', compact('tickets'));
        }

        return view('tickets.index', compact('tickets'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = TicketCategory::all();
        $locations = Location::all();
        if (Auth::user()->role === 'user') {
            $statusSolicitado = TicketStatus::where('name', 'Solicitado')->first();

            if (!$statusSolicitado) {
                return redirect()->route('tickets.index')->with('error', 'No se encontró el estado "Solicitado".');
            }

            return view('tickets.create', compact('categories', 'statusSolicitado', 'locations'));
        }

        // Si es admin, puedes pasarle todos los estados
        $statuses = TicketStatus::all();

        return view('tickets.create', compact('categories', 'statuses', 'locations'));
    }

    public function location()
    {
        return $this->belongsTo(Location::class, 'location');
    }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category_id' => 'required|exists:ticket_categories,id',
            'priority' => 'required|in:baja,media,alta,urgente',
            'marca' => 'nullable|string|max:255',
            'modelo' => 'nullable|string|max:255',
            'numero_serie' => 'nullable|string|max:255',
            'location_id' => 'required|exists:locations,id',
            'usuario' => 'nullable|string|max:255',
            'ip_red_wifi' => 'nullable|string|max:255',
            'cpu' => 'nullable|string|max:255',
            'ram' => 'nullable|string|max:255',
            'capacidad_almacenamiento' => 'nullable|string|max:255',
            'tarjeta_video' => 'nullable|string|max:255',
            'id_anydesk' => 'nullable|string|max:255',
            'pass_anydesk' => 'nullable|string|max:255',
            'version_windows' => 'nullable|string|max:255',
            'licencia_windows' => 'nullable|string|max:255',
            'version_office' => 'nullable|string|max:255',
            'licencia_office' => 'nullable|string|max:255',
            'password_cuenta' => 'nullable|string|max:255',
            'fecha_instalacion' => 'nullable|date',
            'comentarios' => 'nullable|string',
            'contact_phone' => 'nullable|string|max:255',
            'contact_email' => 'nullable|email|max:255',
        ]);

        $ticket = new Ticket($validated);
        $ticket->created_by = Auth::id();

        // Obtener el estado "Solicitado"
        $solicitadoStatus = TicketStatus::where('name', 'Solicitado')->first();
        if (!$solicitadoStatus) {
            // Si no existe el estado "Solicitado", usar "Pendiente" como respaldo
            $solicitadoStatus = TicketStatus::where('name', 'Pendiente')->first();
        }

        if (!$solicitadoStatus) {
            // Si no hay ningún estado disponible, usar el primero que encuentre
            $solicitadoStatus = TicketStatus::first();
        }

        if (!$solicitadoStatus) {
            return redirect()->back()
                ->with('error', 'No se encontró ningún estado disponible para el ticket.')
                ->withInput();
        }

        $ticket->status_id = $solicitadoStatus->id;
        $ticket->save();

        // Cargar relaciones necesarias para evitar nulls
        $ticket->load(['category', 'creator', 'assignedTo']);

        // Guardar archivos adjuntos si existen
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('ticket-documents/' . $ticket->id);
                \App\Models\TicketDocument::create([
                    'ticket_id' => $ticket->id,
                    'user_id' => Auth::id(),
                    'file_name' => $file->getClientOriginalName(),
                    'file_path' => $path,
                    'file_type' => $file->getClientMimeType(),
                    'file_size' => $file->getSize(),
                    'description' => null
                ]);
            }
        }

        // Ejemplo para la creación de ticket (en store):
        $usuariosNotificar = collect();
        if (Auth::user()->isAdmin()) {
            $superadmins = \App\Models\User::where('role', 'superadmin')->get();
            $usuariosNotificar = $usuariosNotificar->merge($superadmins);
            $admins = \App\Models\User::whereIn('role', ['admin', 'superadmin'])->get();
            $usuariosNotificar = $usuariosNotificar->merge($admins);
        }
        $usuariosNotificar = $usuariosNotificar->unique('id');
        $notificationService = app(\App\Services\NotificationService::class);
        foreach ($usuariosNotificar as $usuario) {
            \Log::info('Notificando a usuario único', ['ticket_id' => $ticket->id, 'user_id' => $usuario->id]);
            $notificationService->send(
                $usuario,
                new \App\Notifications\TicketCreatedNotification($ticket, $usuario->id)
            );
        }

        $this->logAudit('Crear Ticket', 'Ticket creado por: ' . Auth::user()->name);
        Mail::to(Auth::user()->email)->send(new TicketCreatedMail($ticket));
        return redirect()->route('tickets.show', $ticket)
            ->with('success', 'Ticket creado exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Ticket $ticket)
    {
        $this->authorize('view', $ticket);

        $ticket->load(['category', 'status', 'creator', 'location', 'assignedTo', 'comments.user', 'documents.user']);
        return view('tickets.show', compact('ticket'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Ticket $ticket)
    {
        $this->authorize('update', $ticket);
        $locations = Location::all();
        $categories = TicketCategory::where('is_active', true)
            ->orderBy('name')
            ->distinct()
            ->get();
        $statuses = TicketStatus::where('is_active', true)
            ->orderBy('name')
            ->select('id', 'name', 'color')
            ->distinct()
            ->get();
        $users = User::all();

        return view('tickets.edit', compact('ticket', 'categories', 'statuses', 'users', 'locations'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Ticket $ticket)
    {
        $this->authorize('update', $ticket);

        $user = Auth::user();
        $rules = [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category_id' => 'required|exists:ticket_categories,id',
            'status_id' => 'required|exists:ticket_statuses,id',
            'priority' => 'required|in:baja,media,alta,urgente',
            'assigned_to' => 'nullable|exists:users,id',
            'solucion_aplicada' => ($request->status_id && TicketStatus::find($request->status_id)?->name === 'Resuelto') ? 'required|string' : 'nullable|string',
        ];
        if ($user->isAdmin() || $user->isSuperadmin()) {
            $rules = array_merge($rules, [
                'location_id' => 'nullable|exists:locations,id',
            'marca' => 'nullable|string|max:255',
            'modelo' => 'nullable|string|max:255',
            'numero_serie' => 'nullable|string|max:255',
            'usuario' => 'nullable|string|max:255',
            'ip_red_wifi' => 'nullable|string|max:255',
            'cpu' => 'nullable|string|max:255',
            'ram' => 'nullable|string|max:255',
            'capacidad_almacenamiento' => 'nullable|string|max:255',
            'tarjeta_video' => 'nullable|string|max:255',
            'id_anydesk' => 'nullable|string|max:255',
            'pass_anydesk' => 'nullable|string|max:255',
            'version_windows' => 'nullable|string|max:255',
            'licencia_windows' => 'nullable|string|max:255',
            'version_office' => 'nullable|string|max:255',
            'licencia_office' => 'nullable|string|max:255',
            'password_cuenta' => 'nullable|string|max:255',
            'fecha_instalacion' => 'nullable|date',
            'comentarios' => 'nullable|string',
            'contact_phone' => 'nullable|string|max:255',
            'contact_email' => 'nullable|email|max:255',
        ]);
        }
        $validated = $request->validate($rules);

        // Solo el asignado puede marcar como Resuelto
        $statusResuelto = TicketStatus::where('name', 'Resuelto')->first();
        $statusCerrado = TicketStatus::where('name', 'Cerrado')->first();
        if ($request->status_id == optional($statusResuelto)->id) {
            if (auth()->id() !== $ticket->assigned_to) {
                return redirect()->back()->with('error', 'Solo el usuario asignado puede marcar el ticket como Resuelto.');
            }
            if (empty($validated['solucion_aplicada'])) {
                return redirect()->back()->with('error', 'Debe ingresar la solución aplicada para resolver el ticket.');
        }
            // Cambiar automáticamente a Cerrado
            $validated['status_id'] = optional($statusCerrado)->id ?? $request->status_id;
        }

        // Guardar los cambios originales para la notificación
        $changes = [];
        foreach ($validated as $field => $newValue) {
            if ($ticket->$field != $newValue) {
                $changes[$field] = [
                    'old' => $ticket->$field,
                    'new' => $newValue
                ];
            }
        }

        // Detectar cambio de prioridad a urgente
        $prioridadAnterior = $ticket->priority;
        $ticket->update($validated);

        // Cargar relaciones necesarias para evitar nulls
        $ticket->load(['category', 'creator', 'assignedTo']);

        // Notificar involucrados si se resolvió/cerró
        if (isset($changes['status_id']) && $validated['status_id'] == optional($statusCerrado)->id) {
            $usuariosNotificar = collect();
            if ($ticket->creator && $ticket->creator->id !== auth()->id()) {
                $usuariosNotificar->push($ticket->creator);
            }
            if ($ticket->assignedTo && $ticket->assignedTo->id !== auth()->id()) {
                $usuariosNotificar->push($ticket->assignedTo);
            }
            $usuariosNotificar = $usuariosNotificar->unique('id');
            foreach ($usuariosNotificar as $usuario) {
                \Log::info('Notificando a usuario único (estado)', ['ticket_id' => $ticket->id, 'user_id' => $usuario->id]);
                $usuario->notify(new \App\Notifications\TicketUpdatedNotification($ticket, $changes, auth()->user(), $usuario->id));
            }
        }

        // Notificar si la prioridad cambió a urgente
        if (
            isset($changes['priority']) &&
            $changes['priority']['new'] === 'urgente' &&
            $changes['priority']['old'] !== 'urgente'
        ) {
            $notificacion = new \App\Notifications\TicketPriorityUrgentNotification($ticket, auth()->user());
            // Notificar al asignado
            if ($ticket->assignedTo) {
                $ticket->assignedTo->notify($notificacion);
            }
            // Notificar a todos los admins y superadmins
            $admins = \App\Models\User::whereIn('role', ['admin', 'superadmin'])->get();
            foreach ($admins as $admin) {
                $admin->notify($notificacion);
        }
        }

        return redirect()->route('tickets.show', $ticket)
            ->with('success', 'Ticket actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Ticket $ticket)
    {
        $this->authorize('delete', $ticket);

        $ticket->delete();
        $this->logAudit('Eliminar Ticket', 'Ticket #' . $ticket->id . ' eliminado por: ' . Auth::user()->name . ', enviado por: ' . ($ticket->creator ? $ticket->creator->name : 'Desconocido'));

        return redirect()->route('tickets.index')
            ->with('success', 'Ticket eliminado exitosamente.');
    }

    /**
     * Add a comment to the ticket.
     */
    public function addComment(Request $request, Ticket $ticket)
    {
        $validated = $request->validate([
            'comment' => 'required|string',
            'is_internal' => 'boolean'
        ]);

        // Asegurarnos que is_internal sea booleano
        $isInternal = $request->has('is_internal') ? true : false;

        $comentario = TicketComment::create([
            'ticket_id' => $ticket->id,
            'user_id' => Auth::id(),
            'comment' => $request->comment,
            'is_internal' => $isInternal,
        ]);

        // Emitir evento broadcast para comentarios en tiempo real
        event(new \App\Events\CommentAdded($ticket, $comentario));

        \Log::info('Nuevo comentario creado', [
            'ticket_id' => $ticket->id,
            'user_id' => Auth::id(),
            'is_internal' => $isInternal,
            'comment' => $request->comment
        ]);

        $this->logAudit('Añadir Comentario', 'Comentario añadido por: ' . Auth::user()->name);

        // Notificar solo si el comentario NO es interno
        if (!$comentario->is_internal) {
            $usuariosNotificar = collect();
            // Notificar a todos los superadmins
            $superadmins = \App\Models\User::where('role', 'superadmin')->get();
            $usuariosNotificar = $usuariosNotificar->merge($superadmins);
            // Notificar a todos los admins
            $admins = \App\Models\User::where('role', 'admin')->get();
            $usuariosNotificar = $usuariosNotificar->merge($admins);
            // Notificar al asignado (si existe)
            if ($ticket->assignedTo) {
                $usuariosNotificar->push($ticket->assignedTo);
            }
            // Notificar al creador
            if ($ticket->creator) {
                $usuariosNotificar->push($ticket->creator);
            }
            // Eliminar duplicados y excluir al usuario que comenta
            $usuariosNotificar = $usuariosNotificar->unique('id')->filter(function($usuario) {
                return $usuario->id !== Auth::id();
            });
            foreach ($usuariosNotificar as $usuario) {
                \Log::info('Notificando a usuario único (comentario)', ['ticket_id' => $ticket->id, 'user_id' => $usuario->id]);
                $usuario->notify(new TicketCommentedNotification($ticket, $comentario, Auth::user(), $usuario->id));
                // Emitir evento personalizado para notificación en vivo
                event(new \App\Events\CommentNotificationBroadcasted(
                    $ticket->id,
                    $comentario->comment,
                    Auth::user()->name,
                    $usuario->id,
                    now()->toDateTimeString()
                ));
            }
        }

        // Si la petición es AJAX, devolver el HTML del comentario
        if ($request->ajax()) {
            $view = view('tickets.partials.comment', [
                'comment' => $comentario,
                'ticket' => $ticket
            ])->render();
            
            return response()->json([
                'success' => true,
                'html' => $view,
                'message' => 'Comentario agregado exitosamente.'
            ]);
        }

        return redirect()->route('tickets.show', $ticket)
            ->with('success', 'Comentario agregado exitosamente.');
    }

    public function updateComment(Request $request, $id)
    {
        $request->validate([
            'comment' => 'required|string|max:1000',
        ]);

        $comment = TicketComment::findOrFail($id);

        // Solo el autor o admin puede editar
        if (Auth::id() !== $comment->user_id && !Auth::user()->isAdmin()) {
            return response()->json(['success' => false], 403);
        }

        $comment->comment = $request->comment;
        $comment->save();
        $this->logAudit('Actualizar Comentario', 'Comentario actualizado por: ' . Auth::user()->name);

        return response()->json(['success' => true, 'updated_comment' => e($comment->comment)]);
    }

    public function deleteComment(Ticket $ticket, TicketComment $comment)
    {
        if (Auth::check() && Auth::id() !== $comment->user_id && !Auth::user()->isAdmin()) {
            // Solo el autor o admin puede eliminar
            if (request()->ajax()) {
                return response()->json(['success' => false, 'message' => 'No tienes permiso para eliminar este comentario.'], 403);
            }
            return redirect()->back()->with('error', 'No tienes permiso para eliminar este comentario.');
        }

        $comment->delete();
        $this->logAudit('Eliminar Comentario', 'Comentario eliminado por: ' . Auth::user()->name);

        // Emitir evento de eliminación en tiempo real
        if (request()->ajax()) {
            event(new \App\Events\CommentDeleted($ticket->id, $comment->id));
        }

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Comentario eliminado correctamente.'
            ]);
        }

        return redirect()->back()->with('success', 'Comentario eliminado correctamente.');
    }

    /**
     * Genera un mensaje de auditoría para los cambios realizados en un ticket.
     *
     * @param  \App\Models\Ticket  $ticket
     * @param  array  $oldValues
     * @param  array  $newValues
     * @return string
     */
    protected function generarMensajeAuditoria($ticket, array $oldValues, array $newValues)
    {
        // Mapas de traducción locales y etiquetas para los campos
        $fields = [
            'title'        => ['label' => 'Título'],
            'description'  => ['label' => 'Descripción'],
            'category_id'  => [
                'label' => 'Categoría',
                'map'   => fn($v) => TicketCategory::find($v)?->name ?? 'Sin categoría'
            ],
            'status_id'    => [
                'label' => 'Estado',
                'map'   => fn($v) => [
                    1 => 'Pendiente',
                    2 => 'En Progreso',
                    3 => 'Resuelto',
                    4 => 'Cerrado',
                ][$v] ?? 'Desconocido'
            ],
            'priority'     => [
                'label' => 'Prioridad',
                'map'   => fn($v) => [
                    'baja'    => 'Baja',
                    'media'   => 'Media',
                    'alta'    => 'Alta',
                    'urgente' => 'Urgente',
                ][$v] ?? ucfirst($v)
            ],
            'assigned_to'  => [
                'label' => 'Asignado a',
                'map'   => fn($v) => $v ? User::find($v)?->name : 'No asignado'
            ],
            // Agrega aquí más campos si los necesitas
        ];

        $mensajes = [];

        foreach ($fields as $campo => $config) {
            // Saltar si el campo no está en los nuevos valores
            if (!array_key_exists($campo, $newValues)) {
                continue;
            }

            $old = $oldValues[$campo] ?? null;
            $new = $newValues[$campo];

            // Aplicar mapeo si existe
            if (isset($config['map'])) {
                $old = $config['map']($old);
                $new = $config['map']($new);
            }

            // Registrar sólo si cambió el valor
            if ((string)$old !== (string)$new) {
                $mensajes[] = "{$config['label']}: de '{$old}' a '{$new}'";
            }
        }

        if (empty($mensajes)) {
            return "Ticket #{$ticket->id} actualizado por: " . Auth::user()->name . ". No hubo cambios.";
        }

        $lista = implode(', ', $mensajes);
        return "Ticket #{$ticket->id} actualizado por: " . Auth::user()->name . ". Cambios: {$lista}";
    }

    public function getComments(Ticket $ticket)
    {
        $this->authorize('view', $ticket);
        
        $comments = $ticket->comments()
            ->with('user')
            ->latest()
            ->get();

        $html = '';
        foreach ($comments as $comment) {
            $html .= view('tickets.partials.comment', [
                'comment' => $comment,
                'ticket' => $ticket
            ])->render();
        }

        return response()->json([
            'success' => true,
            'html' => $html
        ]);
    }
}
