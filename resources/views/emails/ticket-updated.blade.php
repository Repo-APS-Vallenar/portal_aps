<div style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <h2 style="color: #FFA500; margin-bottom: 18px;">✏️ Ticket actualizado</h2>
    <p>¡Hola! 👋<br>Se han realizado cambios en tu ticket. Aquí tienes los detalles:</p>
    <div style="background: #f8fafd; border: 1px solid #e3e8ee; border-radius: 10px; padding: 20px 18px 14px 18px; margin: 18px 0 24px 0; box-shadow: 0 2px 8px #0001;">
        <p style="margin: 0 0 10px 0;"><strong>📝 Título:</strong> {{ $ticket->title }}</p>
        <p style="margin: 0 0 10px 0;"><strong>🔄 Cambios realizados:</strong></p>
        <ul style="margin: 0 0 10px 18px; padding: 0;">
            @foreach($changes as $change)
                <li style="margin-bottom: 6px;">
                    <span style="font-size:1.1em;">👉</span> <strong>{{ $change['label'] }}:</strong> <span style="color:#888;">{{ $change['old'] }}</span> <span style="color:#0d6efd;">→</span> <span style="color:#222; font-weight:bold;">{{ $change['new'] }}</span>
                </li>
            @endforeach
        </ul>
        <p style="margin: 0 0 10px 0;"><strong>🏷️ Categoría:</strong> {{ $ticket->category->name ?? 'Sin categoría' }}</p>
        <p style="margin: 0 0 10px 0;"><strong>👤 Actualizado por:</strong> {{ $updatedBy->name }}</p>
    </div>
    <div style="margin: 24px 0; text-align:center;">
        <a href="{{ url('/tickets/' . $ticket->id) }}" style="display:inline-block; background:#0d6efd; color:#fff; padding:14px 36px; border-radius:8px; text-decoration:none; font-weight:700; font-size:1.12em; box-shadow:0 2px 8px #0d6efd33; letter-spacing:0.5px;">Ver ticket</a>
    </div>
    <hr style="border: 0; border-top: 1px solid #ddd; margin: 20px 0;">
    <p style="font-size: 0.95em; color: #555;">Gracias por usar nuestro sistema de tickets.<br>Este es un mensaje automático, por favor no responder.</p>
    <p style="font-size: 0.95em; color: #555;">¡Saludos! 😊<br>El equipo de soporte de Intranet APS | TBJ</p>
</div>
