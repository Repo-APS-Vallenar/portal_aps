<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use App\Events\NewNotification;

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
    public function send($user, $notificationInstance)
    {
        try {
            if (is_int($user)) {
                $user = \App\Models\User::findOrFail($user);
            }
            $user->notify($notificationInstance);
            return true;
        } catch (\Exception $e) {
            \Log::error('Error al enviar notificación: ' . $e->getMessage(), [
                'user_id' => is_int($user) ? $user : $user->id,
            ]);
            throw $e;
        }
    }

    /**
     * Marca una notificación como leída
     *
     * @param int $notificationId
     * @return bool
     */
    public function markAsRead($notificationId)
    {
        $notification = auth()->user()->notifications()->findOrFail($notificationId);
        $notification->markAsRead();
        return true;
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
            $user = \App\Models\User::findOrFail($user);
        }
        $user->unreadNotifications->markAsRead();
        return true;
    }

    /**
     * Elimina notificaciones antiguas
     *
     * @param int $daysToKeep Número de días a mantener
     * @return int
     */
    public function cleanupOldNotifications(int $daysToKeep = 30)
    {
        try {
            $date = now()->subDays($daysToKeep);
            return Notification::where('created_at', '<', $date)->delete();
        } catch (\Exception $e) {
            Log::error('Error al limpiar notificaciones antiguas: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Obtiene las notificaciones no leídas de un usuario
     *
     * @param User|int $user
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getUnreadNotifications($user)
    {
        if (is_int($user)) {
            $user = \App\Models\User::findOrFail($user);
        }
        return $user->unreadNotifications;
    }
} 