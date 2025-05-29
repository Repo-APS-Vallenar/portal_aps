<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DocumentAdded implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $ticketId;
    public $document;

    public function __construct($ticketId, $document)
    {
        $this->ticketId = $ticketId;
        $this->document = $document;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('ticket.' . $this->ticketId);
    }

    public function broadcastWith()
    {
        return [
            'ticket_id' => $this->ticketId,
            'document' => $this->document
        ];
    }

    public function broadcastAs()
    {
        return 'document-added';
    }
} 