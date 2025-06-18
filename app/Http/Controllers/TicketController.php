<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\TicketCategory;
use App\Models\TicketStatus;
use App\Models\TicketComment;
use App\Models\User;
use App\Models\Location;
use App\Models\EquipmentInventory;
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
use App\Models\EquipmentMaintenanceLog;

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
    public function index(Request $request)
    {
        $user = Auth::user();

        $query = Ticket::with(['category', 'status', 'creator', 'assignedTo']);

        if ($user->isAdmin() || $user->isSuperadmin()) {
            // No hay filtro adicional para administradores
        } else {
            $query->where('created_by', $user->id);
        }

        // Ordenamiento
        $sortable = ['id', 'title', 'category_id', 'status_id', 'priority', 'created_by', 'assigned_to'];
        $sort = $request->get('sort', 'id');
        $direction = $request->get('direction', 'desc');
        if (!in_array($sort, $sortable)) {
            $sort = 'id';
        }
        if (!in_array($direction, ['asc', 'desc'])) {
            $direction = 'desc';
        }
        $query->orderBy($sort, $direction);

        $tickets = $query->paginate(10)->appends($request->except('page'));

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
        $categories = TicketCategory::where('name', 'like', '%(%')->get();
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
            'location_id' => 'required|exists:locations,id',
            'contact_phone' => 'nullable|string|max:255',
            'contact_email' => 'nullable|email|max:255',
            'numero_serie' => 'nullable|string|max:255',
        ]);

        // Si el email de contacto viene vacío, asignar el del usuario autenticado
        if (empty($validated['contact_email'])) {
            $validated['contact_email'] = Auth::user()->email;
        }

        // Definir los estados de tickets relevantes para la lógica del equipo
        $statusEnProceso = TicketStatus::where('name', 'En Proceso')->first();
        $statusPendiente = TicketStatus::where('name', 'Pendiente')->first();
        $statusSolicitado = TicketStatus::where('name', 'Solicitado')->first();
        $statusCancelado = TicketStatus::where('name', 'Cancelado')->first();
        $statusResuelto = TicketStatus::where('name', 'Resuelto')->first();
        $statusCerrado = TicketStatus::where('name', 'Cerrado')->first();

        // Estados que deberían poner el equipo 'En Reparación'
        $enReparacionStatusIds = [
            optional($statusEnProceso)->id,
            optional($statusPendiente)->id,
            optional($statusSolicitado)->id,
        ];

        // Lógica para manejar el EquipmentInventory
        $equipmentInventory = null;
        if ($request->filled('numero_serie')) {
            $equipmentData = [
                'marca' => $request->marca,
                'modelo' => $request->modelo,
                'tipo' => $request->input('tipo', 'Otro'),
                'estado' => $request->input('estado', 'Activo'), // Default a 'Activo' si no se provee
                'usuario' => $request->usuario,
                'box_oficina' => $request->input('box_oficina', ''),
                'location_id' => $request->location_id, // Usar el location_id del ticket para el equipo
                'ip_red_wifi' => $request->ip_red_wifi,
                'cpu' => $request->cpu,
                'ram' => $request->ram,
                'capacidad_almacenamiento' => $request->capacidad_almacenamiento,
                'tarjeta_video' => $request->tarjeta_video,
                'id_anydesk' => $request->id_anydesk,
                'pass_anydesk' => $request->pass_anydesk,
                'version_windows' => $request->version_windows,
                'licencia_windows' => $request->licencia_windows,
                'version_office' => $request->version_office,
                'licencia_office' => $request->licencia_office,
                'password_cuenta' => $request->password_cuenta,
                'fecha_instalacion' => $request->fecha_instalacion,
                'comentarios' => $request->comentarios,
            ];

            $equipmentInventory = EquipmentInventory::firstOrCreate(
                ['numero_serie' => $request->numero_serie],
                $equipmentData
            );

            // Si el equipo ya existía, actualizamos sus datos (incluyendo el location_id)
            if ($equipmentInventory->wasRecentlyCreated === false) {
                $equipmentInventory->update($equipmentData);
            }

            // Lógica para asignar estado 'En Reparación' al equipo al crear un ticket
            // Si el usuario es 'user', el estado siempre será 'Solicitado'.
            $ticketStatusIdForEquipment = Auth::user()->role === 'user' ? optional($statusSolicitado)->id : $request->status_id;

            if (
                $equipmentInventory &&
                in_array($ticketStatusIdForEquipment, $enReparacionStatusIds) &&
                !in_array($equipmentInventory->estado, ['En Reparación', 'Dado de Baja'])
            ) {
                $equipmentInventory->estado = 'En Reparación';
                $equipmentInventory->save();
                Log::info('Equipo ' . $equipmentInventory->numero_serie . ' puesto en estado "En Reparación" debido al estado inicial del ticket.');
            }
        }

        $ticket = new Ticket($validated);
        $ticket->created_by = Auth::id();
        $ticket->equipment_inventory_id = $equipmentInventory ? $equipmentInventory->id : null; // Asignar el ID del equipo

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

        // Guardar archivos adjuntos
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('ticket-documents/' . $ticket->id, 'public');
                $ticket->documents()->create([
                    'file_path' => $path,
                    'file_name' => $file->getClientOriginalName(),
                    'file_type' => $file->getClientMimeType(),
                    'file_size' => $file->getSize(),
                    'user_id' => auth()->id()
                ]);
            }
            \Log::info('Archivos adjuntos guardados para el ticket: ' . $ticket->id);
        }

        // Ejemplo para la creación de ticket (en store):
        $usuariosNotificar = collect();

        if (Auth::user()->isAdmin() || Auth::user()->isSuperadmin()) {
            // Notificar a todos los admin y superadmin, excepto al propio creador
            $admins = \App\Models\User::whereIn('role', ['admin', 'superadmin'])
                ->where('id', '!=', Auth::id())
                ->get();
            $usuariosNotificar = $usuariosNotificar->merge($admins);
        } else {
            // Si es usuario normal, notificar a todos los admin y superadmin
            $admins = \App\Models\User::whereIn('role', ['admin', 'superadmin'])->get();
            $usuariosNotificar = $usuariosNotificar->merge($admins);
        }

        $usuariosNotificar = $usuariosNotificar->unique('id');
        $notificationService = app(\App\Services\NotificationService::class);
        foreach ($usuariosNotificar as $usuario) {
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
            ->where('name', 'like', '%(%)')
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

        // Definir los estados de tickets relevantes para la lógica del equipo
        $statusEnProceso = TicketStatus::where('name', 'En Proceso')->first();
        $statusPendiente = TicketStatus::where('name', 'Pendiente')->first();
        $statusSolicitado = TicketStatus::where('name', 'Solicitado')->first();
        $statusCancelado = TicketStatus::where('name', 'Cancelado')->first();
        $statusResuelto = TicketStatus::where('name', 'Resuelto')->first();
        $statusCerrado = TicketStatus::where('name', 'Cerrado')->first();

        $rules = [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category_id' => 'required|exists:ticket_categories,id',
            'status_id' => 'required|exists:ticket_statuses,id',
            'priority' => 'required|in:baja,media,alta,urgente',
            'assigned_to' => 'nullable|exists:users,id',
            'solucion_aplicada' => ($request->status_id && TicketStatus::find($request->status_id)?->name === 'Resuelto') ? 'required|string' : 'nullable|string',
            'location_id' => 'required|exists:locations,id',
            'numero_serie' => 'nullable|string|max:255',
        ];

        $validated = $request->validate($rules);

        // Guardar los valores originales del ticket antes de la actualización
        $oldEquipmentInventoryId = $ticket->equipment_inventory_id;
        $oldCategoryId = $ticket->category_id;
        $oldStatusId = $ticket->status_id;

        // Lógica para manejar el EquipmentInventory
        $equipmentInventory = null;
        if ($request->filled('equipment_inventory_id')) {
            // Si se seleccionó un equipo existente
            $equipmentInventory = EquipmentInventory::find($request->equipment_inventory_id);
        } elseif ($request->filled('numero_serie')) {
            // Si es un equipo nuevo o un numero de serie para buscar/crear
            $equipmentData = [
                'marca' => $request->marca,
                'modelo' => $request->modelo,
                'tipo' => $request->input('tipo', 'Otro'),
                'estado' => $request->input('estado', 'Activo'), // Default a 'Activo' si no se provee
                'usuario' => $request->usuario,
                'box_oficina' => $request->input('box_oficina', ''),
                'location_id' => $request->location_id,
                'ip_red_wifi' => $request->ip_red_wifi,
                'cpu' => $request->cpu,
                'ram' => $request->ram,
                'capacidad_almacenamiento' => $request->capacidad_almacenamiento,
                'tarjeta_video' => $request->tarjeta_video,
                'id_anydesk' => $request->id_anydesk,
                'pass_anydesk' => $request->pass_anydesk,
                'version_windows' => $request->version_windows,
                'licencia_windows' => $request->licencia_windows,
                'version_office' => $request->version_office,
                'licencia_office' => $request->licencia_office,
                'password_cuenta' => $request->password_cuenta,
                'fecha_instalacion' => $request->fecha_instalacion,
                'comentarios' => $request->comentarios,
            ];

            $equipmentInventory = EquipmentInventory::firstOrCreate(
                ['numero_serie' => $request->numero_serie],
                $equipmentData
            );

            // Si el equipo ya existía, actualizamos sus datos (incluyendo el location_id)
            if ($equipmentInventory->wasRecentlyCreated === false) {
                $equipmentInventory->update($equipmentData);
            }
        }

        // Asignar el ID del equipo al ticket antes de llenar con otros datos
        $ticket->equipment_inventory_id = $equipmentInventory ? $equipmentInventory->id : null;

        $ticket->fill($request->only([
            'title', 'description', 'category_id', 'status_id', 'priority', 'assigned_to',
            'solucion_aplicada', 'contact_phone', 'contact_email', 'location_id'
        ]));

        if ($ticket->isDirty() || $ticket->equipment_inventory_id !== $oldEquipmentInventoryId) {
            // Cargar datos previos del ticket para la auditoría y notificaciones
            $oldValues = $ticket->getOriginal();
            $newValues = $ticket->getDirty();
            $this->logAudit('Actualizar Ticket', $this->generarMensajeAuditoria($ticket, $oldValues, $newValues));

            $ticket->save();

            // Lógica para actualizar el estado del equipo en el inventario
            if ($ticket->equipment_inventory_id) {
                $currentEquipment = EquipmentInventory::find($ticket->equipment_inventory_id);

                if ($currentEquipment) {
                    // Estados que deberían poner el equipo 'En Reparación'
                    $enReparacionStatusIds = [
                        optional($statusEnProceso)->id,
                        optional($statusPendiente)->id,
                        optional($statusSolicitado)->id,
                    ];

                    // Estados que deberían poner el equipo 'Activo'
                    $activoStatusIds = [
                        optional($statusCancelado)->id,
                        optional($statusResuelto)->id,
                        optional($statusCerrado)->id,
                    ];

                    // Caso 1: El ticket cambia a un estado que implica 'En Reparación'
                    if (
                        in_array($request->status_id, $enReparacionStatusIds) &&
                        $currentEquipment->estado !== 'En Reparación' &&
                        $currentEquipment->estado !== 'Dado de Baja' // No cambiar si está dado de baja
                    ) {
                        $currentEquipment->estado = 'En Reparación';
                        $currentEquipment->save();
                        Log::info('Equipo ' . $currentEquipment->numero_serie . ' puesto en estado "En Reparación" debido al estado del ticket.');
                    }

                    // Caso 2: El ticket cambia a un estado que implica 'Activo'
                    if (
                        in_array($request->status_id, $activoStatusIds) &&
                        $currentEquipment->estado === 'En Reparación' // Solo si estaba en reparación
                    ) {
                        // Verificar si existen otros tickets (no el actual) para este equipo que lo mantengan 'En Reparación'
                        // Es decir, tickets que NO estén en estados de 'Activo' (Cancelado, Resuelto, Cerrado)
                        $otherActiveMaintenanceTickets = Ticket::where('equipment_inventory_id', $currentEquipment->id)
                            ->where('id', '!=', $ticket->id) // Excluir el ticket actual
                            ->whereHas('status', function ($query) use ($activoStatusIds) {
                                $query->whereNotIn('id', $activoStatusIds); // Filtrar por estados que NO sean Activo
                            })
                            ->exists();

                        if (!$otherActiveMaintenanceTickets) {
                            $currentEquipment->estado = 'Activo';
                            $currentEquipment->save();
                            Log::info('Equipo ' . $currentEquipment->numero_serie . ' puesto en estado "Activo" porque no hay otros tickets activos que lo mantengan en mantenimiento.');
                        } else {
                            Log::info('Equipo ' . $currentEquipment->numero_serie . ' permanece "En Reparación" porque aún hay otros tickets activos que lo mantienen en mantenimiento.');
                        }
                    }
                }
            }

            // Lógica para registrar mantenimiento de equipo
            if (
                $ticket->equipment_inventory_id &&
                $ticket->status_id == optional($statusResuelto)->id &&
                $request->filled('solucion_aplicada')
            ) {
                try {
                    EquipmentMaintenanceLog::create([
                        'equipment_inventory_id' => $ticket->equipment_inventory_id,
                        'ticket_id' => $ticket->id,
                        'user_id' => Auth::id(),
                        'maintenance_date' => now(),
                        'description_of_work' => $request->solucion_aplicada,
                        'type_of_maintenance' => 'Reparación', // O un valor más dinámico si lo requieres
                    ]);
                    Log::info('Registro de mantenimiento creado para el equipo: ' . $ticket->equipment_inventory_id . ' a través del ticket: ' . $ticket->id);
                } catch (\Exception $e) {
                    Log::error('Error al crear el registro de mantenimiento: ' . $e->getMessage());
                }
            }
        }

        // Cargar relaciones necesarias para evitar nulls
        $ticket->load(['category', 'creator', 'assignedTo']);

        // Notificar cambio de prioridad
        if (isset($newValues['priority']) && $oldValues['priority'] !== $newValues['priority']) {
            $usuariosNotificar = collect();
            
            // Notificar al creador si no es el que hizo el cambio
            if ($ticket->creator && $ticket->creator->id !== auth()->id()) {
                $usuariosNotificar->push($ticket->creator);
            }
            
            // Notificar al asignado si existe y no es el que hizo el cambio
            if ($ticket->assignedTo && $ticket->assignedTo->id !== auth()->id()) {
                $usuariosNotificar->push($ticket->assignedTo);
            }
            
            // Eliminar duplicados y enviar notificaciones
            $usuariosNotificar = $usuariosNotificar->unique('id');
            foreach ($usuariosNotificar as $usuario) {
                $usuario->notify(new \App\Notifications\TicketPriorityChangedNotification(
                    $ticket,
                    auth()->user(),
                    $oldValues['priority'],
                    $newValues['priority'],
                    $usuario->id
                ));
            }
        }

        // Notificar involucrados si se resolvió/cerró
        if (isset($newValues['status_id']) && $newValues['status_id'] == optional($statusCerrado)->id) {
            $usuariosNotificar = collect();
            
            // Notificar al creador si no es el que hizo el cambio
            if ($ticket->creator && $ticket->creator->id !== auth()->id()) {
                $usuariosNotificar->push($ticket->creator);
            }
            
            // Notificar al asignado si existe y no es el que hizo el cambio
            if ($ticket->assignedTo && $ticket->assignedTo->id !== auth()->id()) {
                $usuariosNotificar->push($ticket->assignedTo);
            }
            
            // Eliminar duplicados y enviar notificaciones
            $usuariosNotificar = $usuariosNotificar->unique('id');
            foreach ($usuariosNotificar as $usuario) {
                \Log::info('Notificando a usuario único (estado)', ['ticket_id' => $ticket->id, 'user_id' => $usuario->id]);
                $usuario->notify(new \App\Notifications\TicketUpdatedNotification($ticket, $newValues, auth()->user(), $usuario->id));
            }
        }

        // Detectar reapertura de ticket (de Cerrado o Resuelto a otro estado)
        if (
            isset($newValues['status_id']) &&
            isset($oldValues['status_id'], $newValues['status_id']) &&
            $oldValues['status_id'] != $newValues['status_id']
        ) {
            $statusCerrado = \App\Models\TicketStatus::where('name', 'Cerrado')->first();
            $statusResuelto = \App\Models\TicketStatus::where('name', 'Resuelto')->first();
            $oldStatus = \App\Models\TicketStatus::find($oldValues['status_id']);
            $newStatus = \App\Models\TicketStatus::find($newValues['status_id']);
            $esReapertura = false;
            if (
                ($oldStatus && in_array($oldStatus->name, ['Cerrado', 'Resuelto'])) &&
                ($newStatus && !in_array($newStatus->name, ['Cerrado', 'Resuelto']))
            ) {
                $esReapertura = true;
            }
            if ($esReapertura) {
                $usuariosNotificar = collect();
                if ($ticket->creator && $ticket->creator->id !== auth()->id()) {
                    $usuariosNotificar->push($ticket->creator);
                }
                if ($ticket->assignedTo && $ticket->assignedTo->id !== auth()->id()) {
                    $usuariosNotificar->push($ticket->assignedTo);
                }
                $usuariosNotificar = $usuariosNotificar->unique('id');
                foreach ($usuariosNotificar as $usuario) {
                    $usuario->notify(new \App\Notifications\TicketReopenedNotification(
                        $ticket,
                        auth()->user(),
                        $oldStatus->name,
                        $newStatus->name,
                        $usuario->id
                    ));
                }
            }
        }

        // Notificar cambio de categoría
        if (isset($newValues['category_id']) && $oldValues['category_id'] !== $newValues['category_id']) {
            $oldCategory = \App\Models\TicketCategory::find($oldValues['category_id']);
            $newCategory = \App\Models\TicketCategory::find($newValues['category_id']);
            $usuariosNotificar = collect();
            if ($ticket->creator && $ticket->creator->id !== auth()->id()) {
                $usuariosNotificar->push($ticket->creator);
            }
            if ($ticket->assignedTo && $ticket->assignedTo->id !== auth()->id()) {
                $usuariosNotificar->push($ticket->assignedTo);
            }
            $usuariosNotificar = $usuariosNotificar->unique('id');
            foreach ($usuariosNotificar as $usuario) {
                $usuario->notify(new \App\Notifications\TicketCategoryChangedNotification(
                    $ticket,
                    auth()->user(),
                    $oldCategory ? $oldCategory->name : 'Sin categoría',
                    $newCategory ? $newCategory->name : 'Sin categoría',
                    $usuario->id
                ));
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
        } else {
            // Notificar solo a staff (admins, superadmins y asignado si es staff)
            $usuariosNotificar = collect();
            $superadmins = \App\Models\User::where('role', 'superadmin')->get();
            $usuariosNotificar = $usuariosNotificar->merge($superadmins);
            $admins = \App\Models\User::where('role', 'admin')->get();
            $usuariosNotificar = $usuariosNotificar->merge($admins);
            // Notificar al asignado si es staff
            if ($ticket->assignedTo && in_array($ticket->assignedTo->role, ['admin', 'superadmin'])) {
                $usuariosNotificar->push($ticket->assignedTo);
            }
            // Eliminar duplicados y excluir al usuario que comenta
            $usuariosNotificar = $usuariosNotificar->unique('id')->filter(function($usuario) {
                return $usuario->id !== Auth::id();
            });
            foreach ($usuariosNotificar as $usuario) {
                $usuario->notify(new \App\Notifications\TicketInternalCommentedNotification(
                    $ticket,
                    $comentario->comment,
                    Auth::user(),
                    $usuario->id
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
        if (Auth::check() && Auth::id() !== $comment->user_id && !Auth::user()->isSuperadmin()) {
            // Solo el autor o superadmin puede eliminar
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
