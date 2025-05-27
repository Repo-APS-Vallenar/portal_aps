<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Support\Carbon;
use App\Models\Ticket;

class TicketCreatedNotification extends Notification
{
    use Queueable;

    protected $ticket;

    /**
     * Create a new notification instance.
     */
    public function __construct(Ticket $ticket)
    {
        $this->ticket = $ticket;
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
        \Log::info('Notificación por correo a: ' . $notifiable->email);
        return (new MailMessage)
            ->subject('Nuevo ticket creado: #' . $this->ticket->id)
            ->greeting('¡Hola ' . $notifiable->name . '!')
            ->line('Se ha creado un nuevo ticket en el sistema:')
            ->line('📋 Título: ' . $this->ticket->title)
            ->line('📝 Descripción: ' . $this->ticket->description)
            ->line('🏷️ Categoría: ' . ($this->ticket->category->name ?? 'Sin categoría'))
            ->line('👤 Creado por: ' . $this->ticket->creator->name)
            ->line('⏰ Fecha: ' . $this->ticket->created_at->format('d/m/Y H:i'))
            ->action('Ver ticket', url('/tickets/' . $this->ticket->id))
            ->line('Gracias por usar nuestro sistema de tickets.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toDatabase($notifiable)
    {
        \Log::info('Notificación a base de datos para: ' . $notifiable->id);
        return [
            'ticket_id' => $this->ticket->id,
            'title' => 'Nuevo ticket creado',
            'message' => 'Ticket #' . $this->ticket->id . ': ' . $this->ticket->title,
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
    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'ticket_id' => $this->ticket->id,
            'title' => 'Nuevo ticket creado',
            'message' => 'Ticket #' . $this->ticket->id . ': ' . $this->ticket->title,
            'url' => url('/tickets/' . $this->ticket->id),
            'created_at' => Carbon::now()->toDateTimeString(),
            'data' => [
                'category' => $this->ticket->category->name ?? 'Sin categoría',
                'creator' => $this->ticket->creator->name,
                'description' => $this->ticket->description
            ]
        ]);
    }
}
