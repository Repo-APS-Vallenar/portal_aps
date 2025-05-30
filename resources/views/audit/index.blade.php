@extends('layouts.app')

@section('title', 'Auditoría')

@section('content')
    <div class="container">
        <div class="card mb-4 shadow-sm">
            <div class="card-body">
                <div class="row align-items-center g-2">
                    <div class="row col-md-8">
                        <form method="GET" action="{{ route('audit.index') }}">
                            <div class="row mb-3">
                                <div class="col-md-3">
                                    <label for="user_id">Usuario</label>
                                    <select name="user_id" id="user_id" class="form-select">
                                        <option value="">Todos</option>
                                        @foreach ($usuarios as $usuario)
                                            <option value="{{ $usuario->id }}" {{ request('user_id') == $usuario->id ? 'selected' : '' }}>
                                                {{ $usuario->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label for="action">Acción</label>
                                    <select name="action" id="action" class="form-select">
                                        <option value="">Todas</option>
                                        @foreach ($accionesUnicas as $accion)
                                            <option value="{{ $accion }}" {{ request('action') == $accion ? 'selected' : '' }}>
                                                {{ $accion }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label for="from">Desde</label>
                                    <input type="date" name="from" id="from" class="form-control"
                                        value="{{ request('from') }}">
                                </div>
                                <div class="col-md-3">
                                    <label for="to">Hasta</label>
                                    <input type="date" name="to" id="to" class="form-control" value="{{ request('to') }}">
                                </div>
                            </div>

                            <div class="d-flex flex-column flex-md-row justify-content-between align-items-end gap-2 mt-2">
                                <div class="col-md-6 mb-3">
                                    <input type="text" name="search" class="form-control" placeholder="Buscar..."
                                        value="{{ request('search') }}">
                                </div>
                                <div class="col-md-6 d-flex gap-2 justify-content-md-end justify-content-center mb-3">
                                    <button type="submit" class="btn btn-primary w-100 w-md-auto">Filtrar</button>
                                    <a href="{{ route('audit.index') }}" class="btn btn-secondary w-100 w-md-auto">Limpiar</a>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="col-md-4 text-md-end d-flex justify-content-end gap-2">
                        <div class="d-flex gap-2">
                            <a href="{{ route('audit.export.excel', ['search' => request('search')]) }}"
                                class="btn btn-success"><i class="bi bi-file-earmark-excel me-1"></i>Exportar a Excel</a>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ route('audit.export.pdf', ['search' => request('search')]) }}"
                                class="btn btn-danger"><i class="bi bi-file-earmark-pdf me-1"></i> Exportar a PDF</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div id="audit-table">
            @include('audit.partials.logs', ['logs' => $logs])
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function () {
            $('#btn-search').on('click', function () {
                let value = $('#search').val();
                console.log("Buscando: ", value); 

                $.ajax({
                    url: "{{ route('audit.index') }}",
                    data: { search: value },
                    success: function (data) {
                        console.log("Respuesta AJAX recibida");
                        $('#audit-table').html(data);
                    },
                    error: function (xhr) {
                        console.error("Error AJAX:", xhr.responseText);
                    }
                });
            });

        });
    </script>
@endsection