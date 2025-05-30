@extends('layouts.app')

@section('title', 'Parámetros')

@section('content')
    <div class="container py-4">
        <h2 class="mb-4"><i class="bi bi-gear me-2 text-warning"></i>Parámetros del sistema</h2>
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        <ul class="nav nav-tabs mb-3" id="paramTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="locations-tab" data-bs-toggle="tab" data-bs-target="#locations"
                    type="button" role="tab">Ubicaciones</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="categories-tab" data-bs-toggle="tab" data-bs-target="#categories" type="button"
                    role="tab">Categorías</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="statuses-tab" data-bs-toggle="tab" data-bs-target="#statuses" type="button"
                    role="tab">Estados</button>
            </li>
        </ul>
        <div class="tab-content" id="paramTabsContent">
            <div class="tab-pane fade show active" id="locations" role="tabpanel">
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span><i class="bi bi-geo-alt me-2"></i>Ubicaciones</span>
                        <button class="btn btn-gradient btn-sm" data-bs-toggle="modal" data-bs-target="#addLocationModal"><i
                                class="bi bi-plus-lg"></i> Agregar</button>
                    </div>
                    <div class="card-body p-0">

                        <!-- Tabla escritorio -->
                        <div class="d-none d-md-block">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>Nombre</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($locations as $location)
                                        <tr>
                                            <td>{{ $location->name }}</td>
                                            <td>
                                                <button class="btn btn-warning btn-sm" data-bs-toggle="modal"
                                                    data-bs-target="#editLocationModal{{ $location->id }}"><i
                                                        class="bi bi-pencil"></i></button>
                                                <button class="btn btn-danger btn-sm" data-bs-toggle="modal"
                                                    data-bs-target="#deleteLocationModal{{ $location->id }}"><i
                                                        class="bi bi-trash"></i></button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <!-- Cards móvil -->
                        <div class="d-block d-md-none">
                            @foreach($locations as $location)
                                <div class="card mb-2 shadow-sm border-1" style="min-height: 110px;">
                                    <div class="card-body py-2 px-3 d-flex flex-column justify-content-between h-100">
                                        <div class="fw-bold mb-2" style="word-break: break-word;">{{ $location->name }}</div>
                                        <div class="d-flex justify-content-end gap-2 mt-auto">
                                            <button class="btn btn-warning btn-sm" data-bs-toggle="modal"
                                                data-bs-target="#editLocationModal{{ $location->id }}"><i
                                                    class="bi bi-pencil"></i></button>
                                            <button class="btn btn-danger btn-sm" data-bs-toggle="modal"
                                                data-bs-target="#deleteLocationModal{{ $location->id }}"><i
                                                    class="bi bi-trash"></i></button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="categories" role="tabpanel">
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span><i class="bi bi-tags me-2"></i>Categorías</span>
                        <button class="btn btn-gradient btn-sm" data-bs-toggle="modal" data-bs-target="#addCategoryModal"><i
                                class="bi bi-plus-lg"></i> Agregar</button>
                    </div>
                    <div class="card-body p-0">
                        <!-- Tabla escritorio -->
                        <div class="d-none d-md-block">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Nombre</th>
                                        <th>Color</th>
                                        <th>Activo</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($categories as $category)
                                        <tr>
                                            <td>{{ $category->id }}</td>
                                            <td>{{ $category->name }}</td>
                                            <td><span class="badge"
                                                    style="background: {{ $category->color }}">{{ $category->color }}</span>
                                            </td>
                                            <td>{!! $category->is_active ? '<span class="badge bg-success">Sí</span>' : '<span class="badge bg-secondary">No</span>' !!}
                                            </td>
                                            <td>
                                                <button class="btn btn-warning btn-sm" data-bs-toggle="modal"
                                                    data-bs-target="#editCategoryModal{{ $category->id }}"><i
                                                        class="bi bi-pencil"></i></button>
                                                <button class="btn btn-danger btn-sm" data-bs-toggle="modal"
                                                    data-bs-target="#deleteCategoryModal{{ $category->id }}"><i
                                                        class="bi bi-trash"></i></button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <!-- Cards móvil -->
                        <div class="d-block d-md-none">
                            @foreach($categories as $category)
                                <div class="card mb-2 shadow-sm border-1" style="min-height: 110px;">
                                    <div class="card-body py-2 px-3 d-flex flex-column justify-content-between h-100">
                                        <div class="fw-bold mb-2" style="word-break: break-word;">{{ $category->name }}</div>
                                        <div class="d-flex align-items-center justify-content-between mb-2" style="gap:0.7rem;">
                                            <span class="badge"
                                                style="background: {{ $category->color }}; min-width: 80px;">{{ $category->color }}</span>
                                            <span class="small text-muted">Activo:</span>
                                            {!! $category->is_active ? '<span class="badge bg-info" style="min-width: 50px;">Sí</span>' : '<span class="badge bg-secondary" style="min-width: 50px;">No</span>' !!}
                                        </div>
                                        <div class="d-flex justify-content-end gap-2 mt-auto">
                                            <button class="btn btn-warning btn-sm" data-bs-toggle="modal"
                                                data-bs-target="#editCategoryModal{{ $category->id }}"><i
                                                    class="bi bi-pencil"></i></button>
                                            <button class="btn btn-danger btn-sm" data-bs-toggle="modal"
                                                data-bs-target="#deleteCategoryModal{{ $category->id }}"><i
                                                    class="bi bi-trash"></i></button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="statuses" role="tabpanel">
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span><i class="bi bi-flag me-2"></i>Estados</span>
                        <button class="btn btn-gradient btn-sm" data-bs-toggle="modal" data-bs-target="#addStatusModal"><i
                                class="bi bi-plus-lg"></i> Agregar</button>
                    </div>
                    <div class="card-body p-0">
                        <!-- Tabla escritorio -->
                        <div class="d-none d-md-block">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Nombre</th>
                                        <th>Color</th>
                                        <th>Activo</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($statuses as $status)
                                        <tr>
                                            <td>{{ $status->id }}</td>
                                            <td>{{ $status->name }}</td>
                                            <td><span class="badge"
                                                    style="background: {{ $status->color }}">{{ $status->color }}</span></td>
                                            <td>{!! $status->is_active ? '<span class="badge bg-success">Sí</span>' : '<span class="badge bg-secondary">No</span>' !!}
                                            </td>
                                            <td>
                                                <button class="btn btn-warning btn-sm" data-bs-toggle="modal"
                                                    data-bs-target="#editStatusModal{{ $status->id }}"><i
                                                        class="bi bi-pencil"></i></button>
                                                <button class="btn btn-danger btn-sm" data-bs-toggle="modal"
                                                    data-bs-target="#deleteStatusModal{{ $status->id }}"><i
                                                        class="bi bi-trash"></i></button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <!-- Cards móvil -->
                        <div class="d-block d-md-none">
                            @foreach($statuses as $status)
                                <div class="card mb-2 shadow-sm border-1" style="min-height: 110px;">
                                    <div class="card-body py-2 px-3 d-flex flex-column justify-content-between h-100">
                                        <div class="fw-bold mb-2" style="word-break: break-word;">{{ $status->name }}</div>
                                        <div class="d-flex align-items-center justify-content-between mb-2" style="gap:0.7rem;">
                                            <span class="badge"
                                                style="background: {{ $status->color }}; min-width: 80px;">{{ $status->color }}</span>
                                            <span class="small text-muted">Activo:</span>
                                            {!! $status->is_active ? '<span class="badge bg-info" style="min-width: 50px;">Sí</span>' : '<span class="badge bg-secondary" style="min-width: 50px;">No</span>' !!}
                                        </div>
                                        <div class="d-flex justify-content-end gap-2 mt-auto">
                                            <button class="btn btn-warning btn-sm" data-bs-toggle="modal"
                                                data-bs-target="#editStatusModal{{ $status->id }}"><i
                                                    class="bi bi-pencil"></i></button>
                                            <button class="btn btn-danger btn-sm" data-bs-toggle="modal"
                                                data-bs-target="#deleteStatusModal{{ $status->id }}"><i
                                                    class="bi bi-trash"></i></button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal Agregar Ubicación -->
    <div class="modal fade" id="addLocationModal" tabindex="-1" aria-labelledby="addLocationModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form method="POST" action="{{ route('admin.parameters.locations.store') }}">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addLocationModalLabel">Agregar Ubicación</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="locationName" class="form-label">Nombre</label>
                            <input type="text" class="form-control" id="locationName" name="name" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <!-- Modales Editar y Eliminar por cada ubicación -->
    @foreach($locations as $location)
        <!-- Modal Editar -->
        <div class="modal fade" id="editLocationModal{{ $location->id }}" tabindex="-1"
            aria-labelledby="editLocationModalLabel{{ $location->id }}" aria-hidden="true">
            <div class="modal-dialog">
                <form method="POST" action="{{ route('admin.parameters.locations.update', $location) }}">
                    @csrf
                    @method('PUT')
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editLocationModalLabel{{ $location->id }}">Editar Ubicación</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="editLocationName{{ $location->id }}" class="form-label">Nombre</label>
                                <input type="text" class="form-control" id="editLocationName{{ $location->id }}" name="name"
                                    value="{{ $location->name }}" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-primary">Actualizar</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <!-- Modal Eliminar -->
        <div class="modal fade" id="deleteLocationModal{{ $location->id }}" tabindex="-1"
            aria-labelledby="deleteLocationModalLabel{{ $location->id }}" aria-hidden="true">
            <div class="modal-dialog">
                <form method="POST" action="{{ route('admin.parameters.locations.destroy', $location) }}">
                    @csrf
                    @method('DELETE')
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="deleteLocationModalLabel{{ $location->id }}">Eliminar Ubicación</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            ¿Estás seguro de que deseas eliminar la ubicación <strong>{{ $location->name }}</strong>?
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-danger">Eliminar</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @endforeach
    <!-- Modal Agregar Categoría -->
    <div class="modal fade" id="addCategoryModal" tabindex="-1" aria-labelledby="addCategoryModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form method="POST" action="{{ route('admin.parameters.categories.store') }}">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addCategoryModalLabel">Agregar Categoría</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="categoryName" class="form-label">Nombre</label>
                            <input type="text" class="form-control" id="categoryName" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="categoryColor" class="form-label">Color</label>
                            <input type="color" class="form-control form-control-color" id="categoryColor" name="color"
                                value="#007bff" required>
                        </div>
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" id="categoryActive" name="is_active" value="1"
                                checked>
                            <label class="form-check-label" for="categoryActive">Activo</label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <!-- Modales Editar y Eliminar por cada categoría -->
    @foreach($categories as $category)
        <!-- Modal Editar Categoría -->
        <div class="modal fade" id="editCategoryModal{{ $category->id }}" tabindex="-1"
            aria-labelledby="editCategoryModalLabel{{ $category->id }}" aria-hidden="true">
            <div class="modal-dialog">
                <form method="POST" action="{{ route('admin.parameters.categories.update', $category) }}">
                    @csrf
                    @method('PUT')
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editCategoryModalLabel{{ $category->id }}">Editar Categoría</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="editCategoryName{{ $category->id }}" class="form-label">Nombre</label>
                                <input type="text" class="form-control" id="editCategoryName{{ $category->id }}" name="name"
                                    value="{{ $category->name }}" required>
                            </div>
                            <div class="mb-3">
                                <label for="editCategoryColor{{ $category->id }}" class="form-label">Color</label>
                                <input type="color" class="form-control form-control-color"
                                    id="editCategoryColor{{ $category->id }}" name="color" value="{{ $category->color }}"
                                    required>
                            </div>
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" id="editCategoryActive{{ $category->id }}"
                                    name="is_active" value="1" {{ $category->is_active ? 'checked' : '' }}>
                                <label class="form-check-label" for="editCategoryActive{{ $category->id }}">Activo</label>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-primary">Actualizar</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <!-- Modal Eliminar Categoría -->
        <div class="modal fade" id="deleteCategoryModal{{ $category->id }}" tabindex="-1"
            aria-labelledby="deleteCategoryModalLabel{{ $category->id }}" aria-hidden="true">
            <div class="modal-dialog">
                <form method="POST" action="{{ route('admin.parameters.categories.destroy', $category) }}">
                    @csrf
                    @method('DELETE')
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="deleteCategoryModalLabel{{ $category->id }}">Eliminar Categoría</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            ¿Estás seguro de que deseas eliminar la categoría <strong>{{ $category->name }}</strong>?
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-danger">Eliminar</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @endforeach
    <!-- Modal Agregar Estado -->
    <div class="modal fade" id="addStatusModal" tabindex="-1" aria-labelledby="addStatusModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form method="POST" action="{{ route('admin.parameters.statuses.store') }}">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addStatusModalLabel">Agregar Estado</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="statusName" class="form-label">Nombre</label>
                            <input type="text" class="form-control" id="statusName" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="statusColor" class="form-label">Color</label>
                            <input type="color" class="form-control form-control-color" id="statusColor" name="color"
                                value="#007bff" required>
                        </div>
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" id="statusActive" name="is_active" value="1"
                                checked>
                            <label class="form-check-label" for="statusActive">Activo</label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <!-- Modales Editar y Eliminar por cada estado -->
    @foreach($statuses as $status)
        <!-- Modal Editar Estado -->
        <div class="modal fade" id="editStatusModal{{ $status->id }}" tabindex="-1"
            aria-labelledby="editStatusModalLabel{{ $status->id }}" aria-hidden="true">
            <div class="modal-dialog">
                <form method="POST" action="{{ route('admin.parameters.statuses.update', $status) }}">
                    @csrf
                    @method('PUT')
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editStatusModalLabel{{ $status->id }}">Editar Estado</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="editStatusName{{ $status->id }}" class="form-label">Nombre</label>
                                <input type="text" class="form-control" id="editStatusName{{ $status->id }}" name="name"
                                    value="{{ $status->name }}" required>
                            </div>
                            <div class="mb-3">
                                <label for="editStatusColor{{ $status->id }}" class="form-label">Color</label>
                                <input type="color" class="form-control form-control-color"
                                    id="editStatusColor{{ $status->id }}" name="color" value="{{ $status->color }}" required>
                            </div>
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" id="editStatusActive{{ $status->id }}"
                                    name="is_active" value="1" {{ $status->is_active ? 'checked' : '' }}>
                                <label class="form-check-label" for="editStatusActive{{ $status->id }}">Activo</label>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-primary">Actualizar</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <!-- Modal Eliminar Estado -->
        <div class="modal fade" id="deleteStatusModal{{ $status->id }}" tabindex="-1"
            aria-labelledby="deleteStatusModalLabel{{ $status->id }}" aria-hidden="true">
            <div class="modal-dialog">
                <form method="POST" action="{{ route('admin.parameters.statuses.destroy', $status) }}">
                    @csrf
                    @method('DELETE')
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="deleteStatusModalLabel{{ $status->id }}">Eliminar Estado</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            ¿Estás seguro de que deseas eliminar el estado <strong>{{ $status->name }}</strong>?
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-danger">Eliminar</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @endforeach
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Auto-cierre de alertas después de 5 segundos
            setTimeout(function () {
                document.querySelectorAll('.alert-dismissible').forEach(function (alert) {
                    if (alert.classList.contains('show')) {
                        // Bootstrap 5: usa .alert('close') si está disponible
                        if (typeof bootstrap !== 'undefined' && bootstrap.Alert) {
                            bootstrap.Alert.getOrCreateInstance(alert).close();
                        } else {
                            alert.remove();
                        }
                    }
                });
            }, 5000);
            const urlParams = new URLSearchParams(window.location.search);
            const tab = urlParams.get('tab');
            if (tab) {
                const tabBtn = document.querySelector(`[data-bs-target="#${tab}"]`);
                if (tabBtn) {
                    new bootstrap.Tab(tabBtn).show();
                }
            }
            document.querySelectorAll('#paramTabs button[data-bs-toggle="tab"]').forEach(btn => {
                btn.addEventListener('shown.bs.tab', function (e) {
                    const newTab = e.target.getAttribute('data-bs-target').replace('#', '');
                    const url = new URL(window.location);
                    url.searchParams.set('tab', newTab);
                    window.history.replaceState({}, '', url);
                });
            });
        });
    </script>
@endpush