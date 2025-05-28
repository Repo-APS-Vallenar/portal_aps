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
        auth()->user()->unreadNotifications->markAsRead();
        return response()->json(['success' => true]);
    }

    /**
     * Obtiene las notificaciones del usuario
     */
    public function index(Request $request)
    {
        $notifications = auth()->user()->notifications()->latest()->take(10)->get();

        // Adaptar para el frontend
        $notifications = $notifications->map(function ($noti) {
            $data = $noti->data;
            if (is_string($data)) {
                $data = json_decode($data, true);
            }
            return [
                'id' => $noti->id,
                'title' => $data['title'] ?? $noti->data['title'] ?? '',
                'message' => $data['message'] ?? $noti->data['message'] ?? '',
                'type' => $this->mapType($noti->type, $data),
                'is_read' => is_null($noti->read_at) ? false : true,
                'created_at' => $noti->created_at,
                'link' => $data['url'] ?? null,
                'data' => $data['data'] ?? [],
            ];
        });

        if ($request->ajax() || $request->has('ajax')) {
            return response()->json([
                'notifications' => $notifications
            ]);
        }

        return view('notifications.index', compact('notifications'));
    }

    private function mapType($type, $data)
    {
        if (isset($data['type'])) return $data['type'];
        if (str_contains($type, 'TicketCreatedNotification')) return 'ticket_created';
        if (str_contains($type, 'TicketUpdatedNotification')) return 'ticket_updated';
        if (str_contains($type, 'TicketCommentedNotification')) return 'ticket_commented';
        return 'notification';
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
        // Usar una notificación nativa de Laravel
        $notification = new \App\Notifications\TicketManualNotification($ticket, $user, $user->id);
        $this->notificationService->send($user, $notification);
        Log::info('Notificación creada', ['user_id' => $user->id]);
        return response()->json(['success' => true, 'message' => 'Notificación enviada correctamente.']);
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

    /**
     * Elimina todas las notificaciones leídas del usuario
     */
    public function cleanup()
    {
        try {
            $deleted = auth()->user()->notifications()->whereNotNull('read_at')->delete();

            return response()->json([
                'success' => true,
                'message' => 'Notificaciones leídas eliminadas correctamente',
                'deleted_count' => $deleted
            ]);
        } catch (\Exception $e) {
            \Log::error('Error al limpiar notificaciones: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar las notificaciones'
            ], 500);
        }
    }
}
