<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\BroadcastMessage;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Support\Carbon;

class TicketManualNotification extends Notification
{
    use Queueable;

    protected $ticket;
    protected $user;

    public function __construct(Ticket $ticket, User $user)
    {
        $this->ticket = $ticket;
        $this->user = $user;
    }

    public function via($notifiable)
    {
        return ['database', 'mail', 'broadcast'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'title' => 'Notificación manual de ticket',
            'message' => 'Tienes una notificación manual sobre el ticket #' . $this->ticket->id . ': ' . $this->ticket->title,
            'ticket_id' => $this->ticket->id,
            'user_id' => $this->user->id,
            'created_at' => Carbon::now()->toDateTimeString(),
        ];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Notificación manual de ticket')
            ->greeting('¡Hola ' . $notifiable->name . '!')
            ->line('Tienes una notificación manual sobre el ticket #' . $this->ticket->id . ': ' . $this->ticket->title)
            ->action('Ver ticket', url('/tickets/' . $this->ticket->id))
            ->line('Gracias por usar el sistema.');
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'title' => 'Notificación manual de ticket',
            'message' => 'Tienes una notificación manual sobre el ticket #' . $this->ticket->id . ': ' . $this->ticket->title,
            'ticket_id' => $this->ticket->id,
            'user_id' => $this->user->id,
            'created_at' => Carbon::now()->toDateTimeString(),
        ]);
    }
} 