<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    /**
     * Crea y envía una notificación
     *
     * @param User|int $user Usuario o ID del usuario
     * @param string $type Tipo de notificación
     * @param string $title Título de la notificación
     * @param string $message Mensaje de la notificación
     * @param string|null $link Enlace opcional
     * @param array|null $data Datos adicionales
     * @return Notification
     */
    public function send($user, string $type, string $title, string $message, ?string $link = null, ?array $data = null)
    {
        try {
            // Si se pasa un ID, obtener el usuario
            if (is_int($user)) {
                $user = User::findOrFail($user);
            }

            // Crear la notificación
            $notification = Notification::create([
                'user_id' => $user->id,
                'type' => $type,
                'title' => $title,
                'message' => $message,
                'link' => $link,
                'data' => $data
            ]);

            return $notification;
        } catch (\Exception $e) {
            Log::error('Error al enviar notificación: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Marca una notificación como leída
     *
     * @param int $notificationId
     * @return bool
     */
    public function markAsRead(int $notificationId)
    {
        $notification = Notification::findOrFail($notificationId);
        return $notification->markAsRead();
    }

    /**
     * Marca todas las notificaciones de un usuario como leídas
     *
     * @param User|int $user
     * @return int
     */
    public function markAllAsRead($user)
    {
        if (is_int($user)) {
            $userId = $user;
        } else {
            $userId = $user->id;
        }

        return Notification::where('user_id', $userId)
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now()
            ]);
    }
} 