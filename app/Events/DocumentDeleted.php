<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DocumentDeleted implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $ticketId;
    public $documentId;

    public function __construct($ticketId, $documentId)
    {
        $this->ticketId = $ticketId;
        $this->documentId = $documentId;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('ticket.' . $this->ticketId);
    }

    public function broadcastWith()
    {
        return [
            'ticket_id' => $this->ticketId,
            'document_id' => $this->documentId
        ];
    }

    public function broadcastAs()
    {
        return 'document-deleted';
    }
} 