<?php

namespace App\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\Ticket;
use App\Models\User;

class CommentNotificationBroadcasted implements ShouldBroadcastNow
{
    use Dispatchable, SerializesModels;

    public $ticketId;
    public $commentText;
    public $commenterName;
    public $notifiableId;
    public $createdAt;

    public function __construct($ticketId, $commentText, $commenterName, $notifiableId, $createdAt)
    {
        $this->ticketId = $ticketId;
        $this->commentText = $commentText;
        $this->commenterName = $commenterName;
        $this->notifiableId = $notifiableId;
        $this->createdAt = $createdAt;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('App.Models.User.' . $this->notifiableId);
    }

    public function broadcastWith()
    {
        return [
            'ticket_id' => $this->ticketId,
            'comment' => $this->commentText,
            'commenter_name' => $this->commenterName,
            'created_at' => $this->createdAt,
        ];
    }

    public function broadcastAs()
    {
        return 'comment-notification';
    }
} 