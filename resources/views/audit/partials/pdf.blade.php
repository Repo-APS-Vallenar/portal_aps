<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Bitácora del Sistema</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        th, td {
            border: 1px solid #444;
            padding: 6px;
            text-align: left;
        }
        th {
            background-color: #f1f1f1;
        }
        h2 {
            text-align: center;
        }
    </style>
</head>
<body>
    <h2>Bitácora del Sistema</h2>

    <table>
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Usuario</th>
                <th>Acción</th>
                <th>Descripción</th>
                <th>IP</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($logs as $log)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($log->created_at)->format('d/m/Y H:i') }}</td>
                    <td>{{ $log->user->name ?? 'Sistema' }}</td>
                    <td>{{ $log->action }}</td>
                    <td>{{ $log->description }}</td>
                    <td>{{ $log->ip_address }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
