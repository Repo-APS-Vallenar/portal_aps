<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Support\Carbon;
use App\Models\Ticket;

class TicketAttachmentAddedNotification extends Notification
{
    use Queueable;

    protected $ticket;
    protected $fileName;
    protected $uploadedBy;
    protected $notifiableId;

    public function __construct(Ticket $ticket, $fileName, $uploadedBy, $notifiableId = null)
    {
        $this->ticket = $ticket;
        $this->fileName = $fileName;
        $this->uploadedBy = $uploadedBy;
        $this->notifiableId = $notifiableId;
    }

    public function via($notifiable)
    {
        return ['database', 'mail', 'broadcast'];
    }

    public function toMail($notifiable)
    {
        return (new \Illuminate\Notifications\Messages\MailMessage)
            ->subject('Adjunto agregado al ticket #' . $this->ticket->id . ' | APS | TicketGo')
            ->view('emails.ticket-attachment-added', [
                'ticket' => $this->ticket,
                'attachedBy' => $this->attachedBy ?? $this->uploadedBy ?? null,
            ]);
    }

    public function toDatabase($notifiable)
    {
        return [
            'ticket_id' => $this->ticket->id,
            'title' => 'Nuevo adjunto en el ticket #' . $this->ticket->id,
            'message' => 'Se agregó el archivo "' . $this->fileName . '" al ticket.',
            'file_name' => $this->fileName,
            'uploaded_by' => $this->uploadedBy->name,
            'url' => url('/tickets/' . $this->ticket->id),
            'created_at' => Carbon::now()->toDateTimeString(),
        ];
    }

    public function toBroadcast()
    {
        return new BroadcastMessage([
            'ticket_id' => $this->ticket->id,
            'title' => 'Nuevo adjunto en el ticket #' . $this->ticket->id,
            'message' => 'Se agregó el archivo "' . $this->fileName . '" al ticket.',
            'file_name' => $this->fileName,
            'uploaded_by' => $this->uploadedBy->name,
            'url' => url('/tickets/' . $this->ticket->id),
            'created_at' => Carbon::now()->toDateTimeString(),
        ]);
    }

    public function broadcastOn()
    {
        return new \Illuminate\Broadcasting\PrivateChannel('App.Models.User.' . $this->notifiableId);
    }
} 