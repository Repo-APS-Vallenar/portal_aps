<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\BroadcastMessage;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

class TicketManualNotification extends Notification implements ShouldBroadcastNow
{
    protected $ticket;
    protected $user;
    protected $notifiableId;

    public function __construct(Ticket $ticket, User $user, $notifiableId = null)
    {
        $this->ticket = $ticket;
        $this->user = $user;
        $this->notifiableId = $notifiableId;
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
        return (new \Illuminate\Notifications\Messages\MailMessage)
            ->subject('Notificación manual de ticket #' . $this->ticket->id . ' | APS | TicketGo')
            ->view('emails.ticket-manual', [
                'ticket' => $this->ticket,
                'messageManual' => $this->messageManual,
                'updatedBy' => $this->updatedBy
            ]);
    }

    public function toBroadcast()
    {
        return new BroadcastMessage([
            'title' => 'Notificación manual de ticket',
            'message' => 'Tienes una notificación manual sobre el ticket #' . $this->ticket->id . ': ' . $this->ticket->title,
            'ticket_id' => $this->ticket->id,
            'user_id' => $this->user->id,
            'created_at' => Carbon::now()->toDateTimeString(),
        ]);
    }

    public function broadcastOn()
    {
        return new \Illuminate\Broadcasting\PrivateChannel('App.Models.User.' . $this->notifiableId);
    }
} 