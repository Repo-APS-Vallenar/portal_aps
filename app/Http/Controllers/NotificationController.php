<?php

namespace App\Http\Controllers;

use App\Services\NotificationService;
use Illuminate\Http\Request;
use App\Models\Ticket;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Notifications\DatabaseNotification;

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
    public function markAsRead($id)
    {
        $notification = Auth::user()->notifications()->find($id);
        if ($notification) {
            $notification->markAsRead();
            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false, 'message' => 'Notificación no encontrada'], 404);
    }

    /**
     * Marca todas las notificaciones como leídas
     */
    public function markAllAsRead()
    {
        Auth::user()->unreadNotifications()->update(['read_at' => now()]);
        return response()->json(['success' => true]);
    }

    /**
     * Obtiene las notificaciones del usuario
     */
    public function index(Request $request)
    {
        $notifications = Auth::user()->notifications()->latest()->take(10)->get();

        // Adaptar para el frontend
        $notifications = $notifications->map(function ($noti) {
            $data = $noti->data;
            if (is_string($data)) {
                $data = json_decode($data, true);
            }
            
            $notificationType = $this->mapType($noti->type, $data);
            
            // Asegurar que siempre tenemos title y message
            $title = $data['title'] ?? $this->generateTitle($notificationType, $data);
            $message = $data['message'] ?? $this->generateMessage($notificationType, $data);
            
            return array_merge([
                'id' => $noti->id,
                'type' => $notificationType,
                'title' => $title,
                'message' => $message,
                'is_read' => is_null($noti->read_at) ? false : true,
                'created_at' => $noti->created_at,
                'ticket_id' => $data['ticket_id'] ?? null,
                'data' => $data
            ], $data);
        });

        if ($request->ajax() || $request->has('ajax')) {
            return response()->json([
                'notifications' => $notifications
            ]);
        }

        return view('notifications.index', compact('notifications'));
    }

    private function generateTitle($type, $data)
    {
        $ticketId = $data['ticket_id'] ?? 'N/A';
        
        switch ($type) {
            case 'ticket_created':
                return "Nuevo ticket #{$ticketId} creado";
            case 'ticket_updated':
                return "Ticket #{$ticketId} actualizado";
            case 'ticket_commented':
                return "Nuevo comentario en ticket #{$ticketId}";
            case 'user_enabled':
                return "Tu cuenta ha sido habilitada";
            case 'user_disabled':
                return "Tu cuenta ha sido deshabilitada";
            case 'user_updated':
                return "Tu perfil ha sido actualizado";
            default:
                return "Nueva notificación";
        }
    }

    private function generateMessage($type, $data)
    {
        switch ($type) {
            case 'ticket_created':
                return $data['description'] ?? "Se ha creado un nuevo ticket en el sistema";
            case 'ticket_updated':
                return "El ticket ha sido actualizado por un administrador";
            case 'ticket_commented':
                $comment = $data['comment'] ?? "Nuevo comentario agregado";
                return strlen($comment) > 100 ? substr($comment, 0, 100) . '...' : $comment;
            case 'user_enabled':
                return "Tu cuenta ha sido reactivada y ya puedes acceder al sistema";
            case 'user_disabled':
                return "Tu cuenta ha sido deshabilitada. Contacta al administrador si necesitas ayuda";
            case 'user_updated':
                return "Los datos de tu perfil han sido modificados por un administrador";
            default:
                return "Tienes una nueva notificación";
        }
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
    public function destroy($id)
    {
        $notification = Auth::user()->notifications()->find($id);
        if (!$notification) {
            return response()->json(['success' => false, 'message' => 'Notificación no encontrada'], 404);
        }
        
        // Solo permitir eliminar si está leída
        if (is_null($notification->read_at)) {
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
            $deleted = Auth::user()->notifications()->whereNotNull('read_at')->delete();

            return response()->json([
                'success' => true,
                'message' => 'Notificaciones leídas eliminadas correctamente',
                'deleted_count' => $deleted
            ]);
        } catch (\Exception $e) {
            Log::error('Error al limpiar notificaciones: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar las notificaciones'
            ], 500);
        }
    }
}
