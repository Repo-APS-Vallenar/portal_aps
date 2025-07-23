<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Carbon;
use App\Models\User;

class UserEnabledNotification extends Notification
{
    use Queueable;

    public $user;

    /**
     * Create a new notification instance.
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail', 'broadcast'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'title' => 'Usuario habilitado',
            'message' => 'El usuario ' . $this->user->name . ' ha sido habilitado.',
            'user_id' => $this->user->id,
            'created_at' => \Illuminate\Support\Carbon::now()->toDateTimeString(),
        ];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable)
    {
        return (new \Illuminate\Notifications\Messages\MailMessage)
            ->subject('¡Tu cuenta ha sido habilitada! | APS | TicketGo')
            ->view('emails.user-enabled', [
                'user' => $this->user
            ]);
    }

    public function toBroadcast($notifiable)
    {
        return new \Illuminate\Notifications\Messages\BroadcastMessage([
            'title' => 'Usuario habilitado',
            'message' => 'El usuario ' . $this->user->name . ' ha sido habilitado.',
            'user_id' => $this->user->id,
            'created_at' => \Illuminate\Support\Carbon::now()->toDateTimeString(),
        ]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Usuario habilitado',
            'message' => 'El usuario ' . $this->user->name . ' ha sido habilitado.',
            'user_id' => $this->user->id,
            'created_at' => \Illuminate\Support\Carbon::now()->toDateTimeString(),
        ];
    }
}
