<div style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <h2 style="color: #4CAF50;">Nuevo ticket creado</h2>
    <p><strong>Título:</strong> {{ $ticket->title }}</p>
    <p><strong>Descripción:</strong> {{ $ticket->description }}</p>
    <p><strong>Prioridad:</strong> <span style="color: {{ $ticket->priority === 'alta' ? '#FF0000' : ($ticket->priority === 'media' ? '#FFA500' : '#4CAF50') }};">
        {{ ucfirst($ticket->priority) }}</span></p>
    <hr style="border: 0; border-top: 1px solid #ddd; margin: 20px 0;">
    <p style="font-size: 0.9em; color: #555;">Este es un mensaje automático, por favor no responder.</p>
    <p style="font-size: 0.9em; color: #555;">Saludos,</p>
    <p style="font-size: 0.9em; color: #555;">El equipo de soporte</p>
</div>