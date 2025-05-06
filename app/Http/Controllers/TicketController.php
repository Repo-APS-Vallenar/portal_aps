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

        // Todos los usuarios pueden ver todos los tickets
        $tickets = Ticket::with(['category', 'status', 'creator', 'assignee'])
            ->latest()
            ->paginate(10);

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

        $ticket->load(['category', 'status', 'creator', 'location', 'assignee', 'comments.user']);
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
        // Autorización para editar el ticket
        $this->authorize('update', $ticket);

        // Validación de los datos del request
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category_id' => 'required|exists:ticket_categories,id',
            'status_id' => 'required|exists:ticket_statuses,id',
            'priority' => 'required|in:baja,media,alta,urgente',
            'assigned_to' => 'nullable|exists:users,id',
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

        // Clonamos el ticket antes de la actualización para detectar los cambios
        $originalTicket = clone $ticket;

        // Actualizamos el ticket con los datos validados
        $ticket->update($validated);
        $oldValues      = $originalTicket->getAttributes();
        $newValues      = $ticket->getAttributes();
        $mensajeAuditoria = $this->generarMensajeAuditoria($ticket, $oldValues, $newValues);

        AuditLog::create([
            'user_id'     => Auth::id(),
            'action'      => 'Actualización de ticket',
            'description' => $mensajeAuditoria,
            'ip_address'  => $request->ip(),
        ]);

        // Forzar actualización de `updated_at` si no hubo cambios
        if (!$ticket->wasChanged()) {
            $ticket->touch();
        }

        // Detectar los cambios realizados
        $changes = [];
        foreach ($validated as $key => $newValue) {
            $oldValue = $originalTicket->$key ?? null;

            if ($oldValue != $newValue) {
                $changes[] = ucfirst($key) . ": de '{$oldValue}' a '{$newValue}'";
            }
        }



        // Enviar correo si hay cambios
        if (count($changes) > 0) {
            // Verificar que el creador del ticket tenga un correo válido
            if ($ticket->creator && $ticket->creator->email) {
                // Enviar correo al creador del ticket
                Mail::to($ticket->creator->email)->send(new TicketUpdatedMail($ticket, $changes));
                Log::info('Correo de ticket actualizado enviado a ' . $ticket->creator->email);
            } else {
                Log::warning('No se pudo enviar el correo porque el creador del ticket no tiene un correo válido.');
            }
        }

        // Redirigir a la vista del ticket actualizado
        return redirect()->route('tickets.show', $ticket)
            ->with('success', 'Ticket actualizado exitosamente.');
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

        TicketComment::create([
            'ticket_id' => $ticket->id,
            'user_id' => Auth::id(),
            'comment' => $request->comment,
            'is_internal' => $request->has('is_internal'),
        ]);
        $this->logAudit('Añadir Comentario', 'Comentario añadido por: ' . Auth::user()->name);

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
            return redirect()->back()->with('error', 'No tienes permiso para eliminar este comentario.');
        }

        $comment->delete();
        $this->logAudit('Eliminar Comentario', 'Comentario eliminado por: ' . Auth::user()->name);

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
}
