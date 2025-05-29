<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CommentDeleted implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $ticketId;
    public $commentId;

    public function __construct($ticketId, $commentId)
    {
        $this->ticketId = $ticketId;
        $this->commentId = $commentId;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('ticket.' . $this->ticketId);
    }

    public function broadcastAs()
    {
        return 'comment-deleted';
    }
} 