<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('ticket.{ticketId}', function ($user, $ticketId) {
    $ticket = \App\Models\Ticket::find($ticketId);
    if (!$ticket) return false;
    return $user->id === $ticket->created_by
        || $user->id === $ticket->assigned_to
        || $user->isAdmin()
        || $user->isSuperadmin();
}); 