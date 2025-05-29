<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Support\Carbon;
use App\Models\Ticket;

class TicketReopenedNotification extends Notification
{
    use Queueable;

    protected $ticket;
    protected $updatedBy;
    protected $oldStatus;
    protected $newStatus;
    protected $notifiableId;

    public function __construct(Ticket $ticket, $updatedBy, $oldStatus, $newStatus, $notifiableId = null)
    {
        $this->ticket = $ticket;
        $this->updatedBy = $updatedBy;
        $this->oldStatus = $oldStatus;
        $this->newStatus = $newStatus;
        $this->notifiableId = $notifiableId;
    }

    public function via($notifiable)
    {
        return ['database', 'mail', 'broadcast'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('🔄 Ticket reabierto: #' . $this->ticket->id)
            ->greeting('¡Hola! 👋')
            ->line('El siguiente ticket ha sido reabierto:')
            ->line('📝 *Título:* **' . $this->ticket->title . '**')
            ->line('📄 *Descripción:* ' . $this->ticket->description)
            ->line('🏷️ *Categoría:* ' . ($this->ticket->category->name ?? 'Sin categoría'))
            ->line('👤 *Reabierto por:* ' . $this->updatedBy->name)
            ->line('')
            ->line('Estado: de ' . $this->oldStatus . ' a ' . $this->newStatus)
            ->action('Ver ticket', url('/tickets/' . $this->ticket->id))
            ->line('')
            ->line('¡Saludos! 😊');
    }

    public function toDatabase($notifiable)
    {
        return [
            'ticket_id' => $this->ticket->id,
            'title' => 'Ticket reabierto: #' . $this->ticket->id,
            'message' => 'El ticket fue reabierto (de ' . $this->oldStatus . ' a ' . $this->newStatus . ')',
            'url' => url('/tickets/' . $this->ticket->id),
            'created_at' => Carbon::now()->toDateTimeString(),
            'data' => [
                'category' => $this->ticket->category->name ?? 'Sin categoría',
                'updated_by' => $this->updatedBy->name,
                'old_status' => $this->oldStatus,
                'new_status' => $this->newStatus
            ]
        ];
    }

    public function toBroadcast()
    {
        return new BroadcastMessage([
            'ticket_id' => $this->ticket->id,
            'title' => 'Ticket reabierto: #' . $this->ticket->id,
            'message' => 'El ticket fue reabierto (de ' . $this->oldStatus . ' a ' . $this->newStatus . ')',
            'url' => url('/tickets/' . $this->ticket->id),
            'created_at' => Carbon::now()->toDateTimeString(),
            'data' => [
                'category' => $this->ticket->category->name ?? 'Sin categoría',
                'updated_by' => $this->updatedBy->name,
                'old_status' => $this->oldStatus,
                'new_status' => $this->newStatus
            ]
        ]);
    }

    public function broadcastOn()
    {
        return new \Illuminate\Broadcasting\PrivateChannel('App.Models.User.' . $this->notifiableId);
    }
} 