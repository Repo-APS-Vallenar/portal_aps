<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Support\Carbon;
use App\Models\Ticket;

class TicketInternalCommentedNotification extends Notification
{
    use Queueable;

    protected $ticket;
    protected $comment;
    protected $commenter;
    protected $notifiableId;

    public function __construct(Ticket $ticket, $comment, $commenter, $notifiableId = null)
    {
        $this->ticket = $ticket;
        $this->comment = $comment;
        $this->commenter = $commenter;
        $this->notifiableId = $notifiableId;
    }

    public function via($notifiable)
    {
        return ['database', 'mail', 'broadcast'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('🛡️ Comentario interno en ticket #' . $this->ticket->id)
            ->greeting('¡Hola!')
            ->line('Se ha agregado un comentario interno en el ticket:')
            ->line('👤 *Por:* ' . $this->commenter->name)
            ->line('')
            ->line('Comentario: "' . (mb_strlen($this->comment) > 80 ? mb_substr($this->comment, 0, 80) . '...' : $this->comment) . '"')
            ->action('Ver ticket', url('/tickets/' . $this->ticket->id))
            ->line('Solo visible para el staff.');
    }

    public function toDatabase($notifiable)
    {
        return [
            'ticket_id' => $this->ticket->id,
            'title' => 'Comentario interno en ticket #' . $this->ticket->id,
            'message' => 'Nuevo comentario interno de ' . $this->commenter->name . ': "' . (mb_strlen($this->comment) > 80 ? mb_substr($this->comment, 0, 80) . '...' : $this->comment) . '"',
            'url' => url('/tickets/' . $this->ticket->id),
            'created_at' => Carbon::now()->toDateTimeString(),
            'data' => [
                'commenter' => $this->commenter->name,
                'comment' => $this->comment
            ]
        ];
    }

    public function toBroadcast()
    {
        return new BroadcastMessage([
            'ticket_id' => $this->ticket->id,
            'title' => 'Comentario interno en ticket #' . $this->ticket->id,
            'message' => 'Nuevo comentario interno de ' . $this->commenter->name . ': "' . (mb_strlen($this->comment) > 80 ? mb_substr($this->comment, 0, 80) . '...' : $this->comment) . '"',
            'url' => url('/tickets/' . $this->ticket->id),
            'created_at' => Carbon::now()->toDateTimeString(),
            'data' => [
                'commenter' => $this->commenter->name,
                'comment' => $this->comment
            ]
        ]);
    }

    public function broadcastOn()
    {
        return new \Illuminate\Broadcasting\PrivateChannel('App.Models.User.' . $this->notifiableId);
    }
} 