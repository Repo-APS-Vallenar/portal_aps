<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\BroadcastMessage;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

class TicketAssignedChangedNotification extends Notification implements ShouldBroadcastNow
{
    protected $ticket;
    protected $newAssigned;
    protected $notifiableId;
    protected $customMessage;

    public function __construct(Ticket $ticket, User $newAssigned, $notifiableId = null, $customMessage = null)
    {
        $this->ticket = $ticket;
        $this->newAssigned = $newAssigned;
        $this->notifiableId = $notifiableId;
        $this->customMessage = $customMessage;
    }

    public function via($notifiable)
    {
        return ['database', 'mail', 'broadcast'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'ticket_id' => $this->ticket->id,
            'title' => 'Cambio de asignado',
            'message' => $this->customMessage ?? ('Has sido asignado al ticket #' . $this->ticket->id . ': ' . $this->ticket->title),
            'url' => url('/tickets/' . $this->ticket->id),
            'created_at' => Carbon::now()->toDateTimeString(),
        ];
    }

    public function toMail($notifiable)
    {
        $msg = $this->customMessage ?? ('Has sido asignado al ticket #' . $this->ticket->id . ': ' . $this->ticket->title);
        return (new MailMessage)
            ->subject('Cambio de asignado en el ticket #' . $this->ticket->id)
            ->greeting('¡Hola ' . $notifiable->name . '!')
            ->line($msg)
            ->action('Ver ticket', url('/tickets/' . $this->ticket->id))
            ->line('Gracias por usar nuestro sistema.');
    }

    public function toBroadcast()
    {
        return new BroadcastMessage([
            'ticket_id' => $this->ticket->id,
            'title' => 'Cambio de asignado',
            'message' => $this->customMessage ?? ('Has sido asignado al ticket #' . $this->ticket->id . ': ' . $this->ticket->title),
            'url' => url('/tickets/' . $this->ticket->id),
            'created_at' => Carbon::now()->toDateTimeString(),
        ]);
    }

    public function broadcastOn()
    {
        return new \Illuminate\Broadcasting\PrivateChannel('App.Models.User.' . $this->notifiableId);
    }
} 