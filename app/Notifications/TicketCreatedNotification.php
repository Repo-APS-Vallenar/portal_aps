<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\BroadcastMessage;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

class TicketCreatedNotification extends Notification implements ShouldBroadcastNow
{
    protected $ticket;
    protected $notifiableId;

    /**
     * Create a new notification instance.
     */
    public function __construct(Ticket $ticket, $notifiableId = null)
    {
        $this->ticket = $ticket;
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
        return (new MailMessage)
            ->subject('🎫 Nuevo Ticket Creado: #' . $this->ticket->id)
            ->greeting('¡Hola ' . $notifiable->name . '! 👋')
            ->line('Se ha creado un nuevo ticket en el sistema. Estos son los detalles:')
            ->line('')
            ->line('📝 *Título:* **' . $this->ticket->title . '**')
            ->line('📄 *Descripción:* ' . $this->ticket->description)
            ->line('🏷️ *Categoría:* ' . ($this->ticket->category->name ?? 'Sin categoría'))
            ->line('⚡ *Prioridad:* ' . ucfirst($this->ticket->priority))
            ->line('👤 *Creado por:* ' . $this->ticket->creator->name)
            ->line('')
            ->action('Ver ticket', url('/tickets/' . $this->ticket->id))
            ->line('')
            ->line('Gracias por usar nuestro sistema de tickets. Si tienes dudas, responde a este correo o contacta a soporte.')
            ->line('¡Saludos! 😊');
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
    public function toBroadcast()
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

    public function broadcastOn()
    {
        return new \Illuminate\Broadcasting\PrivateChannel('App.Models.User.' . $this->notifiableId);
    }
}
