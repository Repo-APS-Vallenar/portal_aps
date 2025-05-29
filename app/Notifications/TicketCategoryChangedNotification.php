<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Support\Carbon;
use App\Models\Ticket;

class TicketCategoryChangedNotification extends Notification
{
    use Queueable;

    protected $ticket;
    protected $updatedBy;
    protected $oldCategory;
    protected $newCategory;
    protected $notifiableId;

    public function __construct(Ticket $ticket, $updatedBy, $oldCategory, $newCategory, $notifiableId = null)
    {
        $this->ticket = $ticket;
        $this->updatedBy = $updatedBy;
        $this->oldCategory = $oldCategory;
        $this->newCategory = $newCategory;
        $this->notifiableId = $notifiableId;
    }

    public function via($notifiable)
    {
        return ['database', 'mail', 'broadcast'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('🔄 Cambio de categoría en ticket #' . $this->ticket->id)
            ->greeting('¡Hola! 👋')
            ->line('La categoría del siguiente ticket ha sido modificada:')
            ->line('📝 *Título:* **' . $this->ticket->title . '**')
            ->line('👤 *Actualizado por:* ' . $this->updatedBy->name)
            ->line('')
            ->line('Categoría: de "' . $this->oldCategory . '" a "' . $this->newCategory . '"')
            ->action('Ver ticket', url('/tickets/' . $this->ticket->id))
            ->line('¡Saludos! 😊');
    }

    public function toDatabase($notifiable)
    {
        return [
            'ticket_id' => $this->ticket->id,
            'title' => 'Cambio de categoría en ticket #' . $this->ticket->id,
            'message' => 'La categoría del ticket cambió de "' . $this->oldCategory . '" a "' . $this->newCategory . '"',
            'url' => url('/tickets/' . $this->ticket->id),
            'created_at' => Carbon::now()->toDateTimeString(),
            'data' => [
                'updated_by' => $this->updatedBy->name,
                'old_category' => $this->oldCategory,
                'new_category' => $this->newCategory
            ]
        ];
    }

    public function toBroadcast()
    {
        return new BroadcastMessage([
            'ticket_id' => $this->ticket->id,
            'title' => 'Cambio de categoría en ticket #' . $this->ticket->id,
            'message' => 'La categoría del ticket cambió de "' . $this->oldCategory . '" a "' . $this->newCategory . '"',
            'url' => url('/tickets/' . $this->ticket->id),
            'created_at' => Carbon::now()->toDateTimeString(),
            'data' => [
                'updated_by' => $this->updatedBy->name,
                'old_category' => $this->oldCategory,
                'new_category' => $this->newCategory
            ]
        ]);
    }

    public function broadcastOn()
    {
        return new \Illuminate\Broadcasting\PrivateChannel('App.Models.User.' . $this->notifiableId);
    }
} 