<form id="exportSelectedForm" method="POST" action="{{ route('audit.export.selected') }}">
    @csrf
    <div class="table-responsive-sm">
        <table class="table table-bordered table-striped w-100">
            <thead>
                <tr>
                    <th><input type="checkbox" id="selectAll"></th>
                    <th>Fecha</th>
                    <th>Usuario</th>
                    <th>Acción</th>
                    <th>Descripción</th>
                    <th>IP</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($logs as $log)
                    <tr>
                        <td><input type="checkbox" name="selected_logs[]" value="{{ $log->id }}"></td>
                        <td>{{ $log->created_at->format('d/m/Y H:i') }}</td>
                        <td>{{ $log->user->name ?? 'Sistema' }}</td>
                        <td>{{ $log->action }}</td>
                        <td>{{ $log->description }}</td>
                        <td>{{ $log->ip_address }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">No se encontraron registros.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mt-3 gap-2">
            <div>
                {{ $logs->links() }}
            </div>

            <form id="exportSelectedForm" method="POST" action="{{ route('audit.export.selected') }}">
                @csrf
                <input type="hidden" name="selected_ids" id="selectedIdsInput">
                <button type="submit" class="btn btn-success">
                    <i class="bi bi-file-earmark-excel me-1"></i>Exportar seleccionados a Excel
                </button>
            </form>
        </div>
    </div>
</form>

<script>
    document.getElementById('selectAll').addEventListener('click', function (e) {
        const checkboxes = document.querySelectorAll('input[name="selected_logs[]"]');
        checkboxes.forEach(cb => cb.checked = e.target.checked);
    });
</script>