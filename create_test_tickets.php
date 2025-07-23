<?php

use App\Models\Ticket;
use App\Models\User;
use App\Models\TicketCategory;
use App\Models\TicketStatus;
use App\Models\Location;

$user = User::first();
$categories = TicketCategory::all();
$statuses = TicketStatus::all();
$location = Location::first();

if(!$location) {
    $location = Location::create(['name' => 'CESFAM Vallenar']);
}

// Crear tickets de prueba
$tickets = [
    ['title' => 'Computador no enciende', 'priority' => 'alta', 'status' => 'Solicitado'],
    ['title' => 'Impresora sin toner', 'priority' => 'media', 'status' => 'En Proceso'],
    ['title' => 'Internet lento', 'priority' => 'baja', 'status' => 'Pendiente'],
    ['title' => 'Sistema RAYEN caído', 'priority' => 'urgente', 'status' => 'Solicitado'],
    ['title' => 'Instalación de Office', 'priority' => 'media', 'status' => 'Resuelto'],
];

foreach($tickets as $ticketData) {
    $status = $statuses->where('name', $ticketData['status'])->first();
    $category = $categories->random();
    
    Ticket::create([
        'title' => $ticketData['title'],
        'description' => 'Descripción del ticket: ' . $ticketData['title'],
        'category_id' => $category->id,
        'status_id' => $status->id,
        'created_by' => $user->id,
        'priority' => $ticketData['priority'],
        'location_id' => $location->id,
        'contact_email' => $user->email,
    ]);
}

echo 'Tickets creados: ' . Ticket::count();
