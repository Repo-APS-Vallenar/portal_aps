<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('ticket.{ticketId}', function ($user, $ticketId) {
    // Aquí puedes poner lógica real de permisos, por ahora permitimos a cualquier usuario autenticado
    return (bool) $user;
}); 