<table>
    <thead>
        <tr style="background-color: #f2f2f2;">
            <th style="border: 1px solid #000; font-weight: bold;">Fecha</th>
            <th style="border: 1px solid #000; font-weight: bold;">Usuario</th>
            <th style="border: 1px solid #000; font-weight: bold;">Acción</th>
            <th style="border: 1px solid #000; font-weight: bold;">Descripción</th>
            <th style="border: 1px solid #000; font-weight: bold;">IP</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($logs as $log)
            <tr>
                <td style="border: 1px solid #ccc;">{{ \Carbon\Carbon::parse($log->created_at)->format('d/m/Y H:i') }}</td>
                <td style="border: 1px solid #ccc;">{{ $log->user->name ?? 'Sistema' }}</td>
                <td style="border: 1px solid #ccc;">{{ $log->action }}</td>
                <td style="border: 1px solid #ccc;">{{ $log->description }}</td>
                <td style="border: 1px solid #ccc;">{{ $log->ip_address }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
