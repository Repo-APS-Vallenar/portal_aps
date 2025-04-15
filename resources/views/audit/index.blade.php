@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="card mb-4 shadow-sm">
            <div class="card-body">
                <div class="row align-items-center g-2">
                    <div class="row col-md-8">
                        <div class="col-md-8">
                            <input type="text" id="search" class="form-control"
                                placeholder="Buscar por usuario, acción, descripción o IP">
                        </div>
                        <div class="col-md-4">
                            <button id="btn-search" class="btn btn-primary" type="button">
                                Buscar
                            </button>
                        </div>
                    </div>
                    <div class="col-md-4 text-md-end">
                        <a href="{{ route('export.auditlogs') }}" class="btn btn-success btn-sm me-1">
                            <i class="bi bi-file-earmark-excel me-1"></i> Exportar Bitacora en Excel
                        </a>
                        <a href="{{ route('export.auditlogs.pdf') }}" class="btn btn-danger btn-sm">
                            <i class="bi bi-file-earmark-pdf me-1"></i> Exportar Bitacora en PDF
                        </a>
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
                console.log("Buscando: ", value); // 👈 esto

                $.ajax({
                    url: '{{ route('audit.index') }}',
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