<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Support\Carbon;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

class TicketUpdatedNotification extends Notification implements ShouldBroadcastNow
{
    protected $ticket;
    protected $changes;
    protected $updatedBy;
    protected $notifiableId;

    /**
     * Create a new notification instance.
     */
    public function __construct(Ticket $ticket, array $changes, $updatedBy, $notifiableId = null)
    {
        $this->ticket = $ticket;
        $this->changes = $changes;
        $this->updatedBy = $updatedBy;
        $this->notifiableId = $notifiableId;
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
        foreach ($this->formattedChanges() as $change) {
            $message->line('🔄 ' . $change['label'] . ': ' . $change['old'] . ' → ' . $change['new']);
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
        $formatted = $this->formattedChanges();
        return [
            'ticket_id' => $this->ticket->id,
            'title' => 'Ticket actualizado',
            'message' => 'Se han realizado cambios en el ticket #' . $this->ticket->id,
            'changes' => $formatted,
            'url' => url('/tickets/' . $this->ticket->id),
            'created_at' => Carbon::now()->toDateTimeString(),
            'data' => [
                'category' => $this->ticket->category->name ?? 'Sin categoría',
                'creator' => $this->ticket->creator->name,
                'description' => $this->ticket->description
            ]
        ];
    }

    /**
     * Get the broadcast representation of the notification.
     */
    public function toBroadcast()
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

    public function broadcastOn()
    {
        return new \Illuminate\Broadcasting\PrivateChannel('App.Models.User.' . $this->notifiableId);
    }

    protected function fieldLabel($field)
    {
        $labels = [
            'title' => 'Título',
            'description' => 'Descripción',
            'category_id' => 'Categoría',
            'status_id' => 'Estado',
            'priority' => 'Prioridad',
            'assigned_to' => 'Asignado a',
            'solucion_aplicada' => 'Solución Aplicada',
        ];
        return $labels[$field] ?? $field;
    }

    protected function fieldValue($field, $value)
    {
        if ($field === 'status_id') {
            $map = [
                1 => 'Pendiente',
                2 => 'En Progreso',
                3 => 'Resuelto',
                4 => 'Cerrado',
            ];
            return $map[$value] ?? $value;
        }
        if ($field === 'category_id') {
            return \App\Models\TicketCategory::find($value)?->name ?? 'Sin categoría';
        }
        if ($field === 'assigned_to') {
            return $value ? (\App\Models\User::find($value)?->name ?? 'No asignado') : 'No asignado';
        }
        if ($field === 'priority') {
            $map = [
                'baja' => 'Baja',
                'media' => 'Media',
                'alta' => 'Alta',
                'urgente' => 'Urgente',
            ];
            return $map[$value] ?? $value;
        }
        return $value;
    }

    protected function formattedChanges()
    {
        $result = [];
        foreach ($this->changes as $field => $change) {
            $label = $this->fieldLabel($field);
            $old = $this->fieldValue($field, $change['old']);
            $new = $this->fieldValue($field, $change['new']);
            $result[] = [
                'label' => $label,
                'old' => $old,
                'new' => $new,
            ];
        }
        return $result;
    }
} 