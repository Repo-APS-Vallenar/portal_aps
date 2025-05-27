<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Support\Carbon;
use App\Models\Ticket;

class TicketUpdatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $ticket;
    protected $changes;
    protected $updatedBy;

    /**
     * Create a new notification instance.
     */
    public function __construct(Ticket $ticket, array $changes, $updatedBy)
    {
        $this->ticket = $ticket;
        $this->changes = $changes;
        $this->updatedBy = $updatedBy;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via($notifiable)
    {
        return ['database', 'mail', 'broadcast'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable)
    {
        $message = (new MailMessage)
            ->subject('Ticket actualizado: #' . $this->ticket->id)
            ->greeting('¡Hola ' . $notifiable->name . '!')
            ->line('Se han realizado cambios en tu ticket:')
            ->line('📋 Título: ' . $this->ticket->title);

        // Agregar los cambios específicos
        foreach ($this->changes as $field => $change) {
            $message->line('🔄 ' . $this->formatChange($field, $change));
        }

        $message->line('👤 Actualizado por: ' . $this->updatedBy->name)
            ->line('⏰ Fecha: ' . Carbon::now()->format('d/m/Y H:i'))
            ->action('Ver ticket', url('/tickets/' . $this->ticket->id))
            ->line('Gracias por usar nuestro sistema de tickets.');

        return $message;
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toDatabase($notifiable)
    {
        return [
            'ticket_id' => $this->ticket->id,
            'title' => 'Ticket actualizado',
            'message' => 'Se han realizado cambios en el ticket #' . $this->ticket->id,
            'url' => url('/tickets/' . $this->ticket->id),
            'created_at' => Carbon::now()->toDateTimeString(),
            'data' => [
                'changes' => $this->changes,
                'updated_by' => $this->updatedBy->name,
                'ticket_title' => $this->ticket->title,
                'category' => $this->ticket->category->name ?? 'Sin categoría'
            ]
        ];
    }

    /**
     * Get the broadcast representation of the notification.
     */
    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'ticket_id' => $this->ticket->id,
            'title' => 'Ticket actualizado',
            'message' => 'Se han realizado cambios en el ticket #' . $this->ticket->id,
            'url' => url('/tickets/' . $this->ticket->id),
            'created_at' => Carbon::now()->toDateTimeString(),
            'data' => [
                'changes' => $this->changes,
                'updated_by' => $this->updatedBy->name,
                'ticket_title' => $this->ticket->title,
                'category' => $this->ticket->category->name ?? 'Sin categoría'
            ]
        ]);
    }

    /**
     * Formatea los cambios para mostrarlos de manera legible
     */
    protected function formatChange($field, $change)
    {
        $fieldNames = [
            'status' => 'Estado',
            'priority' => 'Prioridad',
            'category_id' => 'Categoría',
            'assigned_to' => 'Asignado a',
            'title' => 'Título',
            'description' => 'Descripción'
        ];

        $fieldName = $fieldNames[$field] ?? $field;
        
        if (is_array($change)) {
            $old = $change['old'] ?? 'No especificado';
            $new = $change['new'] ?? 'No especificado';
            return "$fieldName: $old → $new";
        }

        return "$fieldName: $change";
    }
} 