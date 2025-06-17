<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Support\Carbon;
use App\Models\Ticket;

class TicketPriorityChangedNotification extends Notification
{
    use Queueable;

    protected $ticket;
    protected $updatedBy;
    protected $oldPriority;
    protected $newPriority;
    protected $notifiableId;

    public function __construct(Ticket $ticket, $updatedBy, $oldPriority, $newPriority, $notifiableId = null)
    {
        $this->ticket = $ticket;
        $this->updatedBy = $updatedBy;
        $this->oldPriority = $oldPriority;
        $this->newPriority = $newPriority;
        $this->notifiableId = $notifiableId;
    }

    public function via($notifiable)
    {
        return ['database', 'mail', 'broadcast'];
    }

    public function toMail($notifiable)
    {
        $priorityColors = [
            'baja' => '#198754',
            'media' => '#0d6efd',
            'alta' => '#dc3545',
            'urgente' => '#fd7e14',
        ];
        return (new \Illuminate\Notifications\Messages\MailMessage)
            ->subject('🔄 Cambio de prioridad en ticket #' . $this->ticket->id . ' | APS | TicketGo')
            ->view('emails.ticket-priority-changed', [
                'ticket' => $this->ticket,
                'updatedBy' => $this->updatedBy,
                'oldPriority' => $this->oldPriority,
                'newPriority' => $this->newPriority,
                'priorityColors' => $priorityColors
            ]);
    }

    public function toDatabase($notifiable)
    {
        return [
            'ticket_id' => $this->ticket->id,
            'title' => 'Cambio de prioridad en ticket #' . $this->ticket->id,
            'message' => 'La prioridad del ticket cambió de ' . ucfirst($this->oldPriority) . ' a ' . ucfirst($this->newPriority),
            'url' => url('/tickets/' . $this->ticket->id),
            'created_at' => Carbon::now()->toDateTimeString(),
            'data' => [
                'category' => $this->ticket->category->name ?? 'Sin categoría',
                'updated_by' => $this->updatedBy->name,
                'old_priority' => $this->oldPriority,
                'new_priority' => $this->newPriority
            ]
        ];
    }

    public function toBroadcast()
    {
        return new BroadcastMessage([
            'ticket_id' => $this->ticket->id,
            'title' => 'Cambio de prioridad en ticket #' . $this->ticket->id,
            'message' => 'La prioridad del ticket cambió de ' . ucfirst($this->oldPriority) . ' a ' . ucfirst($this->newPriority),
            'url' => url('/tickets/' . $this->ticket->id),
            'created_at' => Carbon::now()->toDateTimeString(),
            'data' => [
                'category' => $this->ticket->category->name ?? 'Sin categoría',
                'updated_by' => $this->updatedBy->name,
                'old_priority' => $this->oldPriority,
                'new_priority' => $this->newPriority
            ]
        ]);
    }

    public function broadcastOn()
    {
        return new \Illuminate\Broadcasting\PrivateChannel('App.Models.User.' . $this->notifiableId);
    }
} 