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
        $ticketNumber = $this->ticket->id;
        $msg = $this->customMessage
            ?
            : ('Se asignó el ticket a: ' . $this->newAssigned->name);
        return [
            'ticket_id' => $ticketNumber,
            'title' => 'Cambio de asignado en el ticket #' . $ticketNumber,
            'message' => $msg,
            'url' => url('/tickets/' . $ticketNumber),
            'created_at' => Carbon::now()->toDateTimeString(),
        ];
    }

    public function toMail($notifiable)
    {
        $ticketNumber = $this->ticket->id;
        $msg = $this->customMessage
            ? ('Ticket #' . $ticketNumber . ': ' . $this->customMessage)
            : ('Se asignó el ticket a: ' . $this->newAssigned->name);
        return (new MailMessage)
            ->subject('Cambio de asignado en el ticket #' . $ticketNumber)
            ->greeting('¡Hola ' . $notifiable->name . '!')
            ->line($msg)
            ->action('Ver ticket', url('/tickets/' . $ticketNumber))
            ->line('Gracias por usar nuestro sistema.');
    }

    public function toBroadcast()
    {
        $ticketNumber = $this->ticket->id;
        $msg = $this->customMessage
            ? ('Ticket #' . $ticketNumber . ': ' . $this->customMessage)
            : ('Se asignó el ticket a: ' . $this->newAssigned->name);
        return new BroadcastMessage([
            'ticket_id' => $ticketNumber,
            'title' => 'Cambio de asignado en el ticket #' . $ticketNumber,
            'message' => $msg,
            'url' => url('/tickets/' . $ticketNumber),
            'created_at' => Carbon::now()->toDateTimeString(),
        ]);
    }

    public function broadcastOn()
    {
        return new \Illuminate\Broadcasting\PrivateChannel('App.Models.User.' . $this->notifiableId);
    }
}