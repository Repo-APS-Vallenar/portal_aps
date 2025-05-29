<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\TicketComment;
use App\Models\Ticket;

class CommentAdded implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $ticketId;
    public $commentHtml;

    public function __construct(Ticket $ticket, TicketComment $comment)
    {
        $this->ticketId = $ticket->id;
        // Renderizamos el HTML del comentario para insertar directo
        $this->commentHtml = view('tickets.partials.comment', [
            'comment' => $comment,
            'ticket' => $ticket
        ])->render();
    }

    public function broadcastOn()
    {
        return new PrivateChannel('ticket.' . $this->ticketId);
    }

    public function broadcastAs()
    {
        return 'comment-added';
    }
} 