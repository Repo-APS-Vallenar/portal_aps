<?php

namespace App\Notifications;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\BroadcastMessage;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

class TicketCommentedNotification extends Notification implements ShouldBroadcastNow
{
    protected $ticket;
    protected $comment;
    protected $commenter;
    protected $notifiableId;
    protected $commentText;

    public function __construct(Ticket $ticket, $comment, User $commenter, $notifiableId = null)
    {
        $this->ticket = $ticket;
        // Si es modelo, convertir a array y extraer el texto
        if (is_object($comment) && method_exists($comment, 'toArray')) {
            $commentArr = $comment->toArray();
            $this->commentText = $commentArr['comment'] ?? '';
        } else if (is_array($comment) && isset($comment['comment'])) {
            $this->commentText = $comment['comment'];
        } else {
            $this->commentText = $comment;
        }
        $this->comment = $comment;
        $this->commenter = $commenter;
        $this->notifiableId = $notifiableId;
        \Log::info('NOTIFICACION CONSTRUCTOR', [
            'comment' => $comment,
            'commentText' => $this->commentText,
            'is_object' => is_object($comment),
            'class' => is_object($comment) ? get_class($comment) : null,
            'fields' => is_object($comment) ? get_object_vars($comment) : null
        ]);
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
            'comment' => is_object($this->comment) && isset($this->comment->comment) ? $this->comment->comment : $this->comment,
            'commenter_name' => $this->commenter->name,
            'url' => url('/tickets/' . $this->ticket->id),
            'created_at' => \Carbon\Carbon::now()->toDateTimeString(),
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

    public function toBroadcast()
    {
        \Log::info('Notificación broadcast por comentario a: ' . $this->notifiableId);
        return new BroadcastMessage([
            'ticket_id' => $this->ticket->id,
            'commenter_name' => $this->commenter->name,
            'comment' => $this->comment->comment,
            'title' => 'Nuevo comentario en el ticket',
            'message' => 'Nuevo comentario en el ticket #' . $this->ticket->id . ' por ' . $this->commenter->name,
            'url' => url('/tickets/' . $this->ticket->id),
            'created_at' => Carbon::now()->toDateTimeString(),
        ]);
    }

    public function broadcastOn()
    {
        return new \Illuminate\Broadcasting\PrivateChannel('App.Models.User.' . $this->notifiableId);
    }

    public function broadcastWith()
    {
        \Log::info('BROADCAST DATA', [
            'comment' => $this->comment,
            'commentText' => $this->commentText,
            'is_object' => is_object($this->comment),
            'class' => is_object($this->comment) ? get_class($this->comment) : null,
            'fields' => is_object($this->comment) ? get_object_vars($this->comment) : null
        ]);
        return [
            'ticket_id' => $this->ticket->id,
            'commenter_name' => $this->commenter->name,
            'comment' => $this->commentText,
            'title' => 'Nuevo comentario en el ticket',
            'message' => 'Nuevo comentario en el ticket #' . $this->ticket->id . ' por ' . $this->commenter->name,
            'type' => 'ticket_commented',
            'url' => url('/tickets/' . $this->ticket->id),
            'created_at' => Carbon::now()->toDateTimeString(),
        ];
    }
}