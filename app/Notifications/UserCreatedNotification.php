<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\BroadcastMessage;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Mail\Mailable;

class UserCreatedNotification extends Notification
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
            'title' => 'Nuevo usuario registrado',
            'message' => 'Se ha creado un nuevo usuario: ' . $this->user->name . ' (' . $this->user->email . ') con rol ' . $this->user->role . '.',
            'user_id' => $this->user->id,
            'created_at' => Carbon::now()->toDateTimeString(),
        ];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Nuevo usuario creado')
            ->view('emails.user-created');
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'title' => 'Nuevo usuario registrado',
            'message' => 'Se ha creado un nuevo usuario: ' . $this->user->name . ' (' . $this->user->email . ') con rol ' . $this->user->role . '.',
            'user_id' => $this->user->id,
            'created_at' => Carbon::now()->toDateTimeString(),
        ]);
    }
} 