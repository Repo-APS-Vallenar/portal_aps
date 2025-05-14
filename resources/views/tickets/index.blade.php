@extends('layouts.app')



@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h2 class="mb-0">Tickets</h2>
                        <a href="{{ route('tickets.create') }}" class="btn btn-gradient">
                            <i class="fas fa-plus"></i> Nuevo Ticket
                        </a>
                    </div>
                    <input type="hidden" name="user_id" value="{{ auth()->id() }}">
                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert" id="success-alert">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                            <script>
                                setTimeout(function () {
                                    var alert = document.getElementById('success-alert');
                                    if (alert) {
                                        alert.classList.remove('show');
                                        alert.classList.add('fade');
                                        // Esperamos que la animación de desvanecimiento termine antes de eliminarla
                                        setTimeout(function () {
                                            alert.remove();
                                        }, 150); // Espera el tiempo de la animación de desvanecimiento
                                    }
                                }, 5000); // 5000 milisegundos (5 segundos)
                            </script>
                        @endif

                        @if (session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                            <script>
                                setTimeout(function () {
                                    var alert = document.querySelector('.alert');
                                    if (alert) {
                                        alert.classList.remove('show');
                                        alert.classList.add('fade');
                                    }
                                }, 5000); // 5000 milisegundos (5 segundos)
                            </script>
                        @endif

                        @if($tickets->isEmpty())
                            <div class="alert alert-info">
                                No hay tickets disponibles.
                            </div>
                        @else
                            <div class="table-responsive d-none d-md-block">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Título</th>
                                            <th>Categoría</th>
                                            <th>Estado</th>
                                            <th>Prioridad</th>
                                            <th>Creado por</th>
                                            <th>Asignado a</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($tickets as $ticket)
                                            <tr>
                                                <td>{{ $ticket->id }}</td>
                                                <td>{{ $ticket->title }}</td>
                                                <td>
                                                    <span class="badge" style="background-color: {{ $ticket->category->color }}">
                                                        {{ $ticket->category->name }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="badge"
                                                        style="background-color: {{ $ticket->status->color }}; color: {{ $ticket->status->color == '#FFD700' ? '#000' : '#fff' }}">
                                                        {{ $ticket->status->name }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-{{ $ticket->getPriorityColor() }}">
                                                        {{ $ticket->getPriorityText() }}
                                                    </span>
                                                </td>
                                                <td>{{ $ticket->creator->name }}</td>
                                                <td>{{ $ticket->assignee ? $ticket->assignee->name : 'Sin asignar' }}</td>
                                                <td>
                                                    <div class="d-flex justify-content-end">
                                                        <div class="btn-group" role="group" aria-label="Acciones del ticket">
                                                            <a href="{{ route('tickets.show', $ticket) }}"
                                                                class="btn btn-outline-success btn-sm">
                                                                <i class="fas fa-eye me-1"></i> Detalles
                                                            </a>
                                                            @if(Auth::user()->isAdmin() || Auth::user()->isSuperadmin())
                                                                <a href="{{ route('tickets.edit', $ticket) }}"
                                                                    class="btn btn-outline-warning btn-sm">
                                                                    <i class="fas fa-edit me-1"></i> Editar
                                                                </a>
                                                                <button type="button" class="btn btn-outline-danger btn-sm"
                                                                    onclick="if(confirm('¿Estás seguro de que deseas eliminar este ticket?')) { document.getElementById('delete-ticket-{{ $ticket->id }}').submit(); }">
                                                                    <i class="fas fa-trash me-1"></i> Eliminar
                                                                </button>
                                                            @endif
                                                        </div>
                                                        <form id="delete-ticket-{{ $ticket->id }}"
                                                            action="{{ route('tickets.destroy', $ticket) }}" method="POST"
                                                            class="d-none">
                                                            @csrf
                                                            @method('DELETE')
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            {{-- Cards para tickets en móvil --}}
                            <div class="ticket-card-list d-md-none">
                                @foreach($tickets as $ticket)
                                    <div class="ticket-card mb-3 p-3 shadow-sm rounded">
                                        <div class="d-flex align-items-center mb-2 gap-2">
                                            <span class="badge bg-primary">#{{ $ticket->id }}</span>
                                            <span class="small text-muted">{{ $ticket->created_at->format('d/m/Y H:i') }}</span>
                                        </div>
                                        <div class="mb-1"><span class="ticket-card-label">Título:</span> <span>{{ $ticket->title }}</span></div>
                                        <div class="mb-1"><span class="ticket-card-label">Categoría:</span> <span class="badge" style="background-color: {{ $ticket->category->color }}; white-space: normal; word-break: break-word;">{{ $ticket->category->name }}</span></div>
                                        <div class="mb-1"><span class="ticket-card-label">Estado:</span> <span class="badge" style="background-color: {{ $ticket->status->color }}; color: {{ $ticket->status->color == '#FFD700' ? '#000' : '#fff' }};">{{ $ticket->status->name }}</span></div>
                                        <div class="mb-1"><span class="ticket-card-label">Prioridad:</span> <span class="badge bg-{{ $ticket->getPriorityColor() }}">{{ $ticket->getPriorityText() }}</span></div>
                                        <div class="mb-1"><span class="ticket-card-label">Creado por:</span> <span>{{ $ticket->creator->name }}</span></div>
                                        <div class="mb-2"><span class="ticket-card-label">Asignado a:</span> <span>{{ $ticket->assignee ? $ticket->assignee->name : 'Sin asignar' }}</span></div>
                                        <div class="ticket-card-actions d-flex flex-column align-items-stretch mt-2" style="gap:0.4em;">
                                            <a href="{{ route('tickets.show', $ticket) }}" class="ticket-pill-btn ticket-pill-details" style="width:100%; border-radius:999px; font-size:1em; height:40px; display:flex; align-items:center; justify-content:center; gap:0.4em; background:#01a3d5; color:#fff; border:1.5px solid #01a3d5; font-weight:600; margin-bottom:0;">
                                                <i class="fas fa-eye"></i>
                                                <span>Detalles</span>
                                            </a>
                                            <a href="{{ route('tickets.edit', $ticket) }}" class="ticket-pill-btn ticket-pill-edit" style="width:100%; border-radius:999px; font-size:1em; height:40px; display:flex; align-items:center; justify-content:center; gap:0.4em; background:#ff9800; color:#fff; border:1.5px solid #ff9800; font-weight:600; margin-bottom:0;">
                                                <i class="fas fa-edit"></i>
                                                <span>Editar</span>
                                            </a>
                                            <button type="button" class="ticket-pill-btn ticket-pill-delete" style="width:100%; border-radius:999px; font-size:1em; height:40px; display:flex; align-items:center; justify-content:center; gap:0.4em; background:#e74c3c; color:#fff; border:1.5px solid #e74c3c; font-weight:600; margin-bottom:0;" data-bs-toggle="modal" data-bs-target="#confirmDeleteModal" data-form="delete-ticket-mobile-{{ $ticket->id }}">
                                                <i class="fas fa-trash"></i>
                                                <span>Eliminar</span>
                                            </button>
                                            <form id="delete-ticket-mobile-{{ $ticket->id }}" action="{{ route('tickets.destroy', $ticket) }}" method="POST" class="d-none">
                                                @csrf
                                                @method('DELETE')
                                            </form>
                                        </div>
                                    </div>
                                @endforeach
                                <div class="d-flex justify-content-center mt-3">
                                    {{ $tickets->links() }}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal de confirmación de eliminación --}}
    <div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="confirmDeleteModalLabel">Confirmar eliminación</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    ¿Estás seguro de que deseas eliminar este ticket?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Eliminar</button>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
<script>
    let formToSubmit = null;
    document.addEventListener('DOMContentLoaded', function () {
        var confirmDeleteModal = document.getElementById('confirmDeleteModal');
        confirmDeleteModal.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget;
            var formId = button.getAttribute('data-form');
            formToSubmit = document.getElementById(formId);
        });
        document.getElementById('confirmDeleteBtn').addEventListener('click', function () {
            if (formToSubmit) formToSubmit.submit();
        });
    });
</script>
@endpush