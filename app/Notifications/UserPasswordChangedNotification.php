<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\BroadcastMessage;
use App\Models\User;
use Illuminate\Support\Carbon;

class UserPasswordChangedNotification extends Notification
{
    use Queueable;

    protected $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function via($notifiable)
    {
        return ['database', 'mail', 'broadcast'];
    }

    public function toDatabase($notifiable)
    {
        \Log::info('Notificación a base de datos para: ' . $notifiable->id);
        return [
            'title' => 'Cambio de contraseña de usuario',
            'message' => 'El usuario ' . $this->user->name . ' ha cambiado su contraseña.',
            'user_id' => $this->user->id,
            'created_at' => Carbon::now()->toDateTimeString(),
        ];
    }

    public function toMail($notifiable)
    {
        \Log::info('Notificación por correo a: ' . $notifiable->email);
        return (new MailMessage)
            ->subject('Cambio de contraseña de usuario')
            ->greeting('¡Hola ' . $notifiable->name . '!')
            ->line('El usuario ' . $this->user->name . ' ha cambiado su contraseña.')
            ->line('Gracias por usar el sistema.');
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'title' => 'Cambio de contraseña de usuario',
            'message' => 'El usuario ' . $this->user->name . ' ha cambiado su contraseña.',
            'user_id' => $this->user->id,
            'created_at' => Carbon::now()->toDateTimeString(),
        ]);
    }
} 