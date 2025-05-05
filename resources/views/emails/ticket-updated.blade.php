<div style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; padding: 20px; background-color: #f7f7f7; border-radius: 8px; max-width: 600px; margin: 0 auto;">
    <div style="text-align: center; margin-bottom: 20px;">
        <h2 style="color: #FFA500;">Actualización de Ticket</h2>
        <p style="font-size: 1.1em; color: #555;">Se ha realizado una actualización en tu ticket. A continuación, se detallan los cambios realizados:</p>
    </div>

    <div style="background-color: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);">
        <h3 style="color: #333; font-size: 1.2em;">Detalles del Ticket</h3>
        <p><strong>Descripción:</strong> {{ $ticket->description }}</p>
        <p><strong>Estado:</strong> 
            <span style="color: {{ $ticket->status->color }}; font-weight: bold;">
                {{ $ticket->status->name }}
            </span>
        </p>
        <p><strong>Prioridad:</strong> 
            <span style="color: {{ $ticket->priority === 'alta' ? '#FF0000' : ($ticket->priority === 'media' ? '#FFA500' : '#4CAF50') }}; font-weight: bold;">
                {{ ucfirst($ticket->priority) }}
            </span>
        </p>
    </div>

    <hr style="border: 0; border-top: 1px solid #ddd; margin: 30px 0;">

    <div style="background-color: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);">
        <h3 style="color: #333; font-size: 1.2em;">Cambios Realizados</h3>
        <ul style="list-style: none; padding-left: 0; color: #555;">
            @foreach($changes as $change)
                <li style="margin-bottom: 10px; padding-left: 20px; position: relative;">
                    <span style="position: absolute; left: 0; top: 0; font-size: 16px; color: #FFA500;">&#x2713;</span>
                    {{ $change }}
                </li>
            @endforeach
        </ul>
    </div>

    <hr style="border: 0; border-top: 1px solid #ddd; margin: 30px 0;">

    <div style="text-align: center;">
        <p style="font-size: 0.9em; color: #555;">Este es un mensaje automático, por favor no responder.</p>
        <p style="font-size: 0.9em; color: #555;">Saludos,</p>
        <p style="font-size: 0.9em; color: #555; font-weight: bold;">El equipo de soporte de Intranet APS</p>
    </div>
</div>
