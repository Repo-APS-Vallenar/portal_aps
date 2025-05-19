<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use App\Models\Ticket;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class NotificationController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Marca una notificación como leída
     */
    public function markAsRead(Notification $notification)
    {
        $this->authorize('update', $notification);
        $this->notificationService->markAsRead($notification->id);
        return response()->json(['success' => true]);
    }

    /**
     * Marca todas las notificaciones como leídas
     */
    public function markAllAsRead()
    {
        $this->notificationService->markAllAsRead(auth()->user());
        return response()->json(['success' => true]);
    }

    /**
     * Obtiene las notificaciones del usuario
     */
    public function index(Request $request)
    {
        $notifications = Notification::where('user_id', auth()->id())
            ->latest()
            ->take(5)
            ->get();

        if ($request->ajax() || $request->has('ajax')) {
            return response()->json([
                'notifications' => $notifications
            ]);
        }

        return view('notifications.index', compact('notifications'));
    }

    /**
     * Envía una notificación manual al usuario dueño de un ticket
     */
    public function notifyTicketUser(Request $request, Ticket $ticket)
    {
        Log::info('Intentando notificar usuario', ['ticket_id' => $ticket->id, 'user_id' => $ticket->created_by]);
        $this->authorize('update', $ticket);
        $user = $ticket->creator;
        if (!$user) {
            Log::warning('El ticket no tiene usuario asignado', ['ticket_id' => $ticket->id]);
            return response()->json(['success' => false, 'message' => 'El ticket no tiene usuario asignado.'], 404);
        }
        $title = '#' . $ticket->id;
        $message = 'Ticket de: ' . $user->name . ' (Categoría: ' . ($ticket->category->name ?? 'Sin categoría') . ')';
        $link = route('tickets.show', $ticket);
        $noti = $this->notificationService->send($user, 'ticket_manual', $title, $message, $link);
        Log::info('Notificación creada', ['notification_id' => $noti->id, 'user_id' => $user->id]);
        return response()->json(['success' => true, 'message' => 'Notificación enviada correctamente.', 'notification' => $noti]);
    }

    /**
     * Elimina una notificación
     */
    public function destroy(Notification $notification)
    {
        $this->authorize('delete', $notification);
        // Solo permitir eliminar si está leída
        if (is_null($notification->read_at) && !$notification->is_read) {
            return response()->json([
                'success' => false,
                'message' => 'Debes marcar la notificación como leída antes de poder eliminarla.'
            ], 403);
        }
        $notification->delete();
        return response()->json(['success' => true]);
    }
}
