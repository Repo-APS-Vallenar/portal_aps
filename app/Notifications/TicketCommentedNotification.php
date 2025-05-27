<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\BroadcastMessage;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Support\Carbon;

class TicketCommentedNotification extends Notification
{
    use Queueable;

    protected $ticket;
    protected $comment;
    protected $commenter;

    public function __construct(Ticket $ticket, $comment, User $commenter)
    {
        $this->ticket = $ticket;
        $this->comment = $comment;
        $this->commenter = $commenter;
    }

    public function via($notifiable)
    {
        return ['database', 'mail', 'broadcast'];
    }

    public function toDatabase($notifiable)
    {
        \Log::info('Notificación a base de datos por comentario para: ' . $notifiable->id);
        return [
            'ticket_id' => $this->ticket->id,
            'title' => 'Nuevo comentario en el ticket',
            'message' => 'Nuevo comentario en el ticket #' . $this->ticket->id . ' por ' . $this->commenter->name,
            'comment' => $this->comment->comment,
            'url' => url('/tickets/' . $this->ticket->id),
            'created_at' => Carbon::now()->toDateTimeString(),
        ];
    }

    public function toMail($notifiable)
    {
        \Log::info('Notificación por correo por comentario a: ' . $notifiable->email);
        return (new MailMessage)
            ->subject('Nuevo comentario en el ticket #' . $this->ticket->id)
            ->greeting('¡Hola ' . $notifiable->name . '!')
            ->line('Se ha añadido un nuevo comentario en el ticket:')
            ->line('"' . $this->comment->comment . '"')
            ->action('Ver ticket', url('/tickets/' . $this->ticket->id))
            ->line('Gracias por usar nuestro sistema.');
    }

    public function toBroadcast($notifiable)
    {
        \Log::info('Notificación broadcast por comentario a: ' . $notifiable->id);
        return new BroadcastMessage([
            'ticket_id' => $this->ticket->id,
            'title' => 'Nuevo comentario en el ticket',
            'message' => 'Nuevo comentario en el ticket #' . $this->ticket->id . ' por ' . $this->commenter->name,
            'comment' => $this->comment->comment,
            'url' => url('/tickets/' . $this->ticket->id),
            'created_at' => Carbon::now()->toDateTimeString(),
        ]);
    }
}