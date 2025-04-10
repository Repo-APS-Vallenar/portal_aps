<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\TicketCategory;
use App\Models\TicketStatus;
use App\Models\TicketComment;
use App\Models\User;
use App\Models\Category;
use App\Models\Status;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TicketController extends Controller
{
    /**
     * Constructor del controlador
     */
    public function __construct()
    {
        $this->middleware('auth');
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

        if (auth()->user()->role === 'user') {
            $statusSolicitado = TicketStatus::where('name', 'Solicitado')->first();

            if (!$statusSolicitado) {
                return redirect()->route('tickets.index')->with('error', 'No se encontró el estado "Solicitado".');
            }

            return view('tickets.create', compact('categories', 'statusSolicitado'));
        }

        // Si es admin, puedes pasarle todos los estados
        $statuses = TicketStatus::all();

        return view('tickets.create', compact('categories', 'statuses'));

    }

    /**
     * Store a newly created resource in storage.
     */
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
            'ubicacion' => 'nullable|string|max:255',
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

        return redirect()->route('tickets.show', $ticket)
            ->with('success', 'Ticket creado exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Ticket $ticket)
    {
        $this->authorize('view', $ticket);

        $ticket->load(['category', 'status', 'creator', 'assignee', 'comments.user']);
        return view('tickets.show', compact('ticket'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Ticket $ticket)
    {
        $this->authorize('update', $ticket);

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

        return view('tickets.edit', compact('ticket', 'categories', 'statuses', 'users'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Ticket $ticket)
    {
        $this->authorize('update', $ticket);

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
            'ubicacion' => 'nullable|string|max:255',
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
        ]);

        $ticket->update($validated);

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
            'user_id' => auth()->id(),
            'comment' => $request->comment,
            'is_internal' => $request->has('is_internal'),
        ]);
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
        if (auth()->id() !== $comment->user_id && !auth()->user()->isAdmin()) {
            return response()->json(['success' => false], 403);
        }

        $comment->comment = $request->comment;
        $comment->save();

        return response()->json(['success' => true, 'updated_comment' => e($comment->comment)]);
    }

    public function deleteComment(Ticket $ticket, TicketComment $comment)
    {
        if (auth()->id() !== $comment->user_id && !auth()->user()->isAdmin()) {
            return redirect()->back()->with('error', 'No tienes permiso para eliminar este comentario.');
        }

        $comment->delete();

        return redirect()->back()->with('success', 'Comentario eliminado correctamente.');
    }

}

