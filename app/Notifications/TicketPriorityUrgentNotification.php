<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Support\Carbon;
use App\Models\Ticket;

class TicketPriorityUrgentNotification extends Notification
{
    use Queueable;

    protected $ticket;
    protected $updatedBy;

    public function __construct(Ticket $ticket, $updatedBy)
    {
        $this->ticket = $ticket;
        $this->updatedBy = $updatedBy;
    }

    public function via($notifiable)
    {
        return ['database', 'mail', 'broadcast'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('🚨 Ticket marcado como URGENTE: #' . $this->ticket->id)
            ->greeting('¡Atención! 🚨')
            ->line('La prioridad del siguiente ticket ha sido cambiada a URGENTE:')
            ->line('')
            ->line('📝 *Título:* **' . $this->ticket->title . '**')
            ->line('📄 *Descripción:* ' . $this->ticket->description)
            ->line('🏷️ *Categoría:* ' . ($this->ticket->category->name ?? 'Sin categoría'))
            ->line('👤 *Actualizado por:* ' . $this->updatedBy->name)
            ->line('')
            ->action('Ver ticket', url('/tickets/' . $this->ticket->id))
            ->line('')
            ->line('Por favor, atiende este ticket con máxima prioridad.')
            ->line('¡Saludos! 😊');
    }

    public function toDatabase($notifiable)
    {
        return [
            'ticket_id' => $this->ticket->id,
            'title' => 'Ticket marcado como URGENTE',
            'message' => 'La prioridad del ticket #' . $this->ticket->id . ' ha sido cambiada a URGENTE.',
            'url' => url('/tickets/' . $this->ticket->id),
            'created_at' => Carbon::now()->toDateTimeString(),
            'data' => [
                'category' => $this->ticket->category->name ?? 'Sin categoría',
                'updated_by' => $this->updatedBy->name,
                'description' => $this->ticket->description
            ]
        ];
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'ticket_id' => $this->ticket->id,
            'title' => 'Ticket marcado como URGENTE',
            'message' => 'La prioridad del ticket #' . $this->ticket->id . ' ha sido cambiada a URGENTE.',
            'url' => url('/tickets/' . $this->ticket->id),
            'created_at' => Carbon::now()->toDateTimeString(),
            'data' => [
                'category' => $this->ticket->category->name ?? 'Sin categoría',
                'updated_by' => $this->updatedBy->name,
                'description' => $this->ticket->description
            ]
        ]);
    }
} 