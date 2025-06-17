@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Inventario de Equipos</h3>
                    <div class="d-flex gap-2">
                        <a href="{{ route('equipment-inventory.create') }}" class="btn btn-primary btn-sm" data-bs-toggle="tooltip" title="Nuevo Equipo">
                            <i class="fas fa-plus"></i> Nuevo Equipo
                        </a>
                        <a href="{{ route('equipment-inventory.export') }}" class="btn btn-success btn-sm" data-bs-toggle="tooltip" title="Exportar a Excel">
                            <i class="fas fa-file-excel"></i> Exportar a Excel
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Filtros -->
                    <form action="{{ route('equipment-inventory.index') }}" method="GET">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
                            <div class="d-flex gap-2 flex-wrap">
                                <div class="form-group flex-grow-1">
                                    <input type="text" name="search" class="form-control" placeholder="Buscar..." value="{{ request('search') }}">
                                </div>
                                <div class="form-group flex-grow-1">
                                    <select name="ubicacion" class="form-control">
                                        <option value="">Todas los Centros</option>
                                        @foreach($locations as $id => $name)
                                            <option value="{{ $id }}" {{ request('ubicacion') == $id ? 'selected' : '' }}>{{ $name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">Filtrar</button>
                                <a href="{{ route('equipment-inventory.index') }}" class="btn btn-secondary">Limpiar</a>
                            </div>
                        </div>
                    </form>

                    @if($equipment->isEmpty())
                        <div class="alert alert-info text-center" role="alert">
                            <h4 class="alert-heading">¡No hay equipos registrados!</h4>
                            <p>Parece que aún no se ha añadido ningún equipo al inventario. Puedes empezar a agregar uno haciendo clic en el botón "Nuevo Equipo".</p>
                            <hr>
                            <p class="mb-0">Si has aplicado filtros, prueba a limpiarlos para ver todos los equipos.</p>
                        </div>
                    @else
                        <!-- Tabla (visible solo en pantallas medianas y grandes) -->
                        <div class="table-responsive d-none d-md-block">
                            <table class="table table-hover table-striped table-bordered table-sm">
                                <thead class="bg-dark text-white">
                                    <tr>
                                        <th>Marca</th>
                                        <th>Modelo</th>
                                        <th>N° Serie</th>
                                        <th>Centro</th>
                                        <th>Usuario</th>
                                        <th>Box/Oficina</th>
                                        <th>Estado</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($equipment as $equipo)
                                        <tr>
                                            <td>{{ $equipo->marca }}</td>
                                            <td>{{ $equipo->modelo }}</td>
                                            <td>{{ $equipo->numero_serie }}</td>
                                            <td>{{ $equipo->location->name ?? 'N/A' }}</td>
                                            <td>{{ $equipo->usuario }}</td>
                                            <td>{{ $equipo->box_oficina }}</td>
                                            <td>
                                                <span class="badge bg-{{ $equipo->estado == 'Activo' ? 'success' : ($equipo->estado == 'En Reparación' ? 'warning' : ($equipo->estado == 'Inactivo' ? 'secondary' : 'danger')) }}">
                                                    {{ $equipo->estado }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm" role="group" aria-label="Acciones de Equipo">
                                                    <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#viewModal" data-equipment-id="{{ $equipo->id }}">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#editModal" data-equipment-id="{{ $equipo->id }}">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal" data-equipment-id="{{ $equipo->id }}">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center">No se encontraron equipos</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Tarjetas para equipos en móvil (visible solo en pantallas pequeñas) -->
                        <div class="equipment-card-list d-md-none">
                            @forelse($equipment as $equipo)
                                <div class="equipment-card" style="position:relative;">
                                    <div style="display:flex;align-items:center;gap:0.7em;">
                                        <span class="badge bg-primary" style="font-size:1em;padding:0.22rem 1.1rem;border-radius:12px;">
                                            #{{ $equipo->id }}
                                        </span>
                                    </div>
                                    <div class="equipment-card-row">
                                        <span class="equipment-card-label">Marca:</span>
                                        <span class="fw-bold">{{ $equipo->marca }}</span>
                                    </div>
                                    <div class="equipment-card-row">
                                        <span class="equipment-card-label">Modelo:</span>
                                        <span>{{ $equipo->modelo }}</span>
                                    </div>
                                    <div class="equipment-card-row">
                                        <span class="equipment-card-label">N° Serie:</span>
                                        <span>{{ $equipo->numero_serie }}</span>
                                    </div>
                                    <div class="equipment-card-row">
                                        <span class="equipment-card-label">Centro:</span>
                                        <span>{{ $equipo->location->name ?? 'N/A' }}</span>
                                    </div>
                                    <div class="equipment-card-row">
                                        <span class="equipment-card-label">Usuario:</span>
                                        <span>{{ $equipo->usuario }}</span>
                                    </div>
                                    <div class="equipment-card-row">
                                        <span class="equipment-card-label">Box/Oficina:</span>
                                        <span>{{ $equipo->box_oficina }}</span>
                                    </div>
                                    <div class="equipment-card-row">
                                        <span class="equipment-card-label">Estado:</span>
                                        <span>
                                            <span class="badge bg-{{ $equipo->estado == 'Activo' ? 'success' : ($equipo->estado == 'En Reparación' ? 'warning' : ($equipo->estado == 'Inactivo' ? 'secondary' : 'danger')) }}">
                                                {{ $equipo->estado }}
                                            </span>
                                        </span>
                                    </div>
                                    <div class="equipment-card-actions" style="gap:0.6rem; margin-top: 10px;">
                                        <button type="button" class="btn btn-info btn-sm ticket-pill-btn flex-fill" data-bs-toggle="modal" data-bs-target="#viewModal" data-equipment-id="{{ $equipo->id }}">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button type="button" class="btn btn-warning btn-sm ticket-pill-btn flex-fill" data-bs-toggle="modal" data-bs-target="#editModal" data-equipment-id="{{ $equipo->id }}">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button type="button" class="btn btn-danger btn-sm ticket-pill-btn flex-fill" data-bs-toggle="modal" data-bs-target="#deleteModal" data-equipment-id="{{ $equipo->id }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            @empty
                                <div class="alert alert-info text-center mt-3">No se encontraron equipos</div>
                            @endforelse
                        </div>

                        <!-- Paginación -->
                        <div class="d-flex justify-content-center mt-4">
                            {{ $equipment->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modales -->
<!-- Modal Ver (Vacío, se llenará con JS) -->
<div class="modal fade" id="viewModal" tabindex="-1" aria-labelledby="viewModalLabel" aria-hidden="true">
    <div class="modal-dialog" style="max-width: 700px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewModalLabel">Detalles del Equipo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="viewModalBody">
                <!-- Contenido cargado dinámicamente -->
                <div class="d-flex justify-content-center align-items-center" style="min-height: 200px;">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Cargando...</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Editar (Vacío, se llenará con JS) -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog" style="max-width: 700px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">Editar Equipo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="editModalBody">
                <!-- Contenido cargado dinámicamente -->
                <div class="d-flex justify-content-center align-items-center" style="min-height: 200px;">
                    <div class="spinner-border text-warning" role="status">
                        <span class="visually-hidden">Cargando...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Eliminar (se llenará con JS) -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirmar Eliminación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="deleteModalBody">
                <!-- Contenido cargado dinámicamente -->
                ¿Estás seguro de que deseas eliminar este equipo?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <form id="deleteForm" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Eliminar</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Script para carga dinámica de modales -->
@push('scripts')
<script type="module">
    document.addEventListener('DOMContentLoaded', function() {
        var viewModal = document.getElementById('viewModal');
        viewModal.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget;
            var equipmentId = button.getAttribute('data-equipment-id');
            var modalBody = viewModal.querySelector('#viewModalBody');
            modalBody.innerHTML = `
                <div class="d-flex justify-content-center align-items-center" style="min-height: 200px;">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Cargando...</span>
                    </div>
                </div>
            `;
            
            fetch(`/equipment-inventory/${equipmentId}/show-partial`)
                .then(response => response.text())
                .then(html => {
                    modalBody.innerHTML = html;
                })
                .catch(error => {
                    console.error('Error cargando detalles del equipo:', error);
                    modalBody.innerHTML = '<div class="alert alert-danger">Error al cargar los detalles.</div>';
                });
        });

        var editModal = document.getElementById('editModal');
        editModal.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget;
            var equipmentId = button.getAttribute('data-equipment-id');
            var modalBody = editModal.querySelector('#editModalBody');
            modalBody.innerHTML = `
                <div class="d-flex justify-content-center align-items-center" style="min-height: 200px;">
                    <div class="spinner-border text-warning" role="status">
                        <span class="visually-hidden">Cargando...</span>
                    </div>
                </div>
            `;

            fetch(`/equipment-inventory/${equipmentId}/edit-partial`)
                .then(response => response.text())
                .then(html => {
                    modalBody.innerHTML = html;
                })
                .catch(error => {
                    console.error('Error cargando formulario de edición:', error);
                    modalBody.innerHTML = '<div class="alert alert-danger">Error al cargar el formulario.</div>';
                });
        });

        var deleteModal = document.getElementById('deleteModal');
        deleteModal.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget;
            var equipmentId = button.getAttribute('data-equipment-id');
            var deleteForm = deleteModal.querySelector('#deleteForm');
            var modalBody = deleteModal.querySelector('#deleteModalBody');
            
            // Actualiza la acción del formulario
            deleteForm.action = `/equipment-inventory/${equipmentId}`;
            
            // Opcional: Actualiza el texto de confirmación
            fetch(`/equipment-inventory/${equipmentId}/show-partial`)
                .then(response => response.text())
                .then(html => {
                    // Extraer los datos relevantes del HTML para el mensaje de confirmación
                    const tempDiv = document.createElement('div');
                    tempDiv.innerHTML = html;
                    const marca = tempDiv.querySelector('td').textContent;
                    const modelo = tempDiv.querySelector('td:nth-child(2)').textContent;
                    const numeroSerie = tempDiv.querySelector('td:nth-child(3)').textContent;
                    modalBody.innerHTML = `¿Estás seguro de que deseas eliminar el equipo <strong>${marca} ${modelo} (${numeroSerie})</strong>?`;
                })
                .catch(error => {
                    console.error('Error cargando datos para confirmación de eliminación:', error);
                    modalBody.innerHTML = '¿Estás seguro de que deseas eliminar este equipo?';
                });
        });

        deleteModal.addEventListener('hidden.bs.modal', function (event) {
            var deleteForm = deleteModal.querySelector('#deleteForm');
            deleteForm.action = ''; // Limpiar la acción del formulario al cerrar el modal
        });
    });
</script>
@endpush

@push('styles')
<style>
    @media (max-width: 767px) { /* Small devices (landscape phones, 767px and down) */
        .table-responsive {
            display: none !important;
        }

        .equipment-card-list {
            display: flex !important;
            flex-direction: column !important;
            gap: 1.5rem !important;
            margin-bottom: 1.5rem;
            padding-left: 0 !important;
            padding-right: 0 !important;
        }

        .equipment-card {
            background: #fff;
            border-radius: 14px;
            box-shadow: 0 2px 8px rgba(1,163,213,0.07), 0 1.5px 4px rgba(0,0,0,0.03);
            border: 1px solid #e3e8ee;
            padding: 0.9rem 0.7rem 1.1rem 0.7rem !important;
            display: flex;
            flex-direction: column;
            gap: 0.3rem;
            align-items: flex-start;
            position: relative;
            width: 100% !important;
            max-width: 100vw !important;
            margin-left: 0 !important;
            margin-right: 0 !important;
            box-sizing: border-box;
        }

        .equipment-card-row {
            margin-bottom: 0.1rem;
            width: 100%;
            display: flex;
            align-items: center;
        }

        .equipment-card-label {
            font-weight: 600;
            color: #01a3d5;
            min-width: 80px;
            font-size: 0.97em;
            margin-right: 0.5em;
        }

        .equipment-card:not(:last-child)::after {
            content: '';
            display: block;
            position: absolute;
            left: 8%;
            right: 8%;
            bottom: -0.75rem;
            height: 1.5px;
            background: #e3e8ee;
            border-radius: 2px;
        }

        .equipment-card-actions {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            width: 100%;
            margin-top: 10px;
        }

        .equipment-card-actions .btn {
            flex-grow: 1;
            min-width: 100px; /* Adjust as needed */
            margin: 5px; /* Spacing between buttons */
        }
    }
</style>
@endpush
@endsection 