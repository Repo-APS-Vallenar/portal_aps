<form id="exportSelectedForm" method="POST" action="{{ route('audit.export.selected') }}">
    @csrf
    <div class="table-responsive-sm d-none d-md-block">
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

    {{-- Cards para auditoría en móvil --}}
    <div class="audit-card-list d-md-none">
        @forelse($logs as $log)
            <div class="audit-card mb-3 p-3 shadow-sm rounded">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="badge bg-primary">{{ $log->created_at->format('d/m/Y H:i') }}</span>
                    <span class="small text-muted">IP: {{ $log->ip_address }}</span>
                </div>
                <div class="mb-1"><strong>Usuario:</strong> {{ $log->user->name ?? 'Sistema' }}</div>
                <div class="mb-1"><strong>Acción:</strong> {{ $log->action }}</div>
                <div class="mb-2"><strong>Descripción:</strong> <span class="text-secondary">{{ $log->description }}</span></div>
                <div class="d-flex gap-2 mt-2">
                    <input type="checkbox" name="selected_logs[]" value="{{ $log->id }}">
                    <span class="small">Seleccionar para exportar</span>
                </div>
            </div>
        @empty
            <div class="alert alert-info">No se encontraron registros.</div>
        @endforelse
        <div class="d-flex justify-content-center mt-3">
            {{ $logs->links() }}
        </div>
        <form id="exportSelectedFormMobile" method="POST" action="{{ route('audit.export.selected') }}" class="mt-2">
            @csrf
            <input type="hidden" name="selected_ids" id="selectedIdsInputMobile">
            <button type="submit" class="btn btn-success w-100">
                <i class="bi bi-file-earmark-excel me-1"></i>Exportar seleccionados a Excel
            </button>
        </form>
    </div>
</form>

<script>
    document.getElementById('selectAll').addEventListener('click', function (e) {
        const checkboxes = document.querySelectorAll('input[name="selected_logs[]"]');
        checkboxes.forEach(cb => cb.checked = e.target.checked);
    });
</script>