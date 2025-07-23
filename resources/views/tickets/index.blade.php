@extends('layouts.app')

@section('title', 'Tickets')

@push('styles')
<style>
    /* Botón Nuevo Ticket mejorado */
    .btn-new-ticket {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        color: white;
        font-weight: 600;
        padding: 0.75rem 1.5rem;
        border-radius: 12px;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
        position: relative;
        overflow: hidden;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .btn-new-ticket:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(102, 126, 234, 0.6);
        color: white;
        text-decoration: none;
    }

    .btn-new-ticket:active {
        transform: translateY(0);
    }

    .btn-new-ticket::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
        transition: left 0.5s;
    }

    .btn-new-ticket:hover::before {
        left: 100%;
    }

    .btn-new-ticket i {
        font-size: 1rem;
        filter: drop-shadow(0 1px 2px rgba(0,0,0,0.2));
    }

    /* Responsive para botones */
    @media (max-width: 768px) {
        .btn-new-ticket {
            padding: 0.6rem 1.2rem;
            font-size: 0.9rem;
        }
    }
</style>
@endpush

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
                    <h2 class="mb-0">Tickets</h2>
                    <div class="d-flex gap-2 flex-wrap">
                        <!-- Toggle de vista -->
                        <div class="btn-group" role="group">
                            <a href="{{ route('tickets.index') }}" class="btn btn-primary">
                                <i class="fas fa-list"></i> Lista
                            </a>
                            <a href="{{ route('tickets.kanban') }}" class="btn btn-outline-primary">
                                <i class="fas fa-columns"></i> Kanban
                            </a>
                        </div>
                        <a href="{{ route('tickets.create') }}" class="btn btn-new-ticket">
                            <i class="fas fa-plus"></i> Nuevo Ticket
                        </a>
                    </div>
                </div>
                <input type="hidden" name="user_id" value="{{ auth()->id() }}">
                <div class="card-body">
                    @if(session('success'))
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            showAlert(@json(session('success')), 'success', document.querySelector('.container'), 5000);
                        });
                    </script>
                    @endif

                    @if (session('error'))
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            showAlert(@json(session('error')), 'danger', document.querySelector('.container'), 5000);
                        });
                    </script>
                    @endif

                    @auth
                    @if(Auth::user()->isAdmin() || Auth::user()->isSuperadmin())
                        <form id="ticket-filters-form" method="GET" action="{{ route('tickets.index') }}">
                            <div class="row mb-3 align-items-end g-2">
                                <div class="col-md-3">
                                    <label for="user-search" class="form-label">Buscar usuario</label>
                                    <input type="text" id="user-search" name="user_name" class="form-control" placeholder="Nombre o email del usuario..." value="{{ request('user_name') }}">
                                    <input type="hidden" id="user-id" name="user_id" value="{{ request('user_id') }}">
                                    <div id="user-suggestions" class="list-group position-absolute w-100" style="z-index: 1000;"></div>
                                </div>
                                <div class="col-md-2">
                                    <label for="status_id" class="form-label">Estado</label>
                                    <select class="form-select" id="status_id" name="status_id">
                                        <option value="">Todos</option>
                                        @foreach(\App\Models\TicketStatus::orderBy('name')->get() as $status)
                                        <option value="{{ $status->id }}" @if(request('status_id')==$status->id) selected @endif>{{ $status->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label for="priority" class="form-label">Prioridad</label>
                                    <select class="form-select" id="priority" name="priority">
                                        <option value="">Todas</option>
                                        @foreach(\App\Models\Ticket::$priorities as $priority)
                                        <option value="{{ $priority }}" @if(request('priority')==$priority) selected @endif>{{ ucfirst($priority) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label for="date_from" class="form-label">Desde</label>
                                    <input type="date" id="date_from" name="date_from" class="form-control" value="{{ request('date_from') }}">
                                </div>
                                <div class="col-md-2">
                                    <label for="date_to" class="form-label">Hasta</label>
                                    <input type="date" id="date_to" name="date_to" class="form-control" value="{{ request('date_to') }}">
                                </div>
                                <div class="col-12 mt-2">
                                    <div class="d-flex flex-wrap gap-2 justify-content-end">
                                        <button type="submit" class="btn btn-primary px-4 fw-bold"><i class="fas fa-search me-1"></i>Buscar</button>
                                        <a href="{{ route('tickets.export', request()->all()) }}" class="btn btn-success px-4 fw-bold" target="_blank"><i class="fas fa-file-excel me-1"></i>Exportar a Excel</a>
                                        <button type="button" id="clear-user-filter" class="btn btn-outline-secondary" @if(!request('user_id') && !request('status_id') && !request('priority') && !request('date_from') && !request('date_to')) style="display:none;" @endif><i class="fas fa-eraser me-1"></i>Limpiar</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    @endif
                    @endauth

                    <div id="tickets-list-container">
                        @if($tickets->isEmpty())
                        <div class="alert" style="background: #c8f4fc; color: #222; border-radius: 10px; border: 1px solid #b6e6f7;">
                            No hay tickets disponibles.
                        </div>
                        @else
                        <div class="table-responsive d-none d-md-block">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        @php
                                        $columns = [
                                        'id' => 'ID',
                                        'created_by' => 'Creado por',
                                        'category_id' => 'Categoría',
                                        'status_id' => 'Estado',
                                        'priority' => 'Prioridad',
                                        'title' => 'Usuario del equipo',
                                        'assigned_to' => 'Asignado a',
                                        ];
                                        $currentSort = request('sort', 'id');
                                        $currentDirection = request('direction', 'desc');
                                        @endphp
                                        @foreach($columns as $col => $label)
                                        <th>
                                            @php
                                            $newDirection = ($currentSort === $col && $currentDirection === 'asc') ? 'desc' : 'asc';
                                            $icon = '';
                                            if ($currentSort === $col) {
                                            $icon = $currentDirection === 'asc' ? '↑' : '↓';
                                            }
                                            @endphp
                                            <a href="{{ request()->fullUrlWithQuery(['sort' => $col, 'direction' => $newDirection, 'page' => null]) }}" style="text-decoration:none; color:inherit;">
                                                {{ $label }} {!! $icon !!}
                                            </a>
                                        </th>
                                        @endforeach
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($tickets as $ticket)
                                    <tr>
                                        <td>{{ $ticket->id }}</td>
                                        <td>{{ $ticket->creator->name }}</td>
                                        <td>
                                            <span class="badge" style="background-color: {{ e($ticket->category->color) }}">
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
                                        <td>{{ $ticket->title }}</td>
                                        <td>{{ $ticket->assignedTo ? $ticket->assignedTo->name : 'Sin asignar' }}</td>
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
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#confirmDeleteModal"
                                                        data-form="delete-ticket-{{ $ticket->id }}">
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
                                    @empty
                                    <tr>
                                        <td colspan="8" class="text-center text-muted" style="background: #c8f4fc; color: #222; border-radius: 10px;">
                                            No hay tickets disponibles.
                                        </td>
                                    </tr>
                                    @endforelse
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
                                <div class="mb-1"><span class="ticket-card-label">Solicitado por:</span>
                                    <span>{{ $ticket->title }}</span>
                                </div>
                                <div class="mb-1"><span class="ticket-card-label">Categoría:</span> <span class="badge"
                                        style="background-color: {{ $ticket->category->color }}; white-space: normal; word-break: break-word;">{{ $ticket->category->name }}</span>
                                </div>
                                <div class="mb-1"><span class="ticket-card-label">Estado:</span> <span class="badge"
                                        style="background-color: {{ $ticket->status->color }}; color: {{ $ticket->status->color == '#FFD700' ? '#000' : '#fff' }};">{{ $ticket->status->name }}</span>
                                </div>
                                <div class="mb-1"><span class="ticket-card-label">Prioridad:</span> <span
                                        class="badge bg-{{ $ticket->getPriorityColor() }}">{{ $ticket->getPriorityText() }}</span>
                                </div>
                                <div class="mb-1"><span class="ticket-card-label">Creado por:</span>
                                    <span>{{ $ticket->creator->name }}</span>
                                </div>
                                <div class="mb-2"><span class="ticket-card-label">Asignado a:</span>
                                    <span>{{ $ticket->assignedTo ? $ticket->assignedTo->name : 'Sin asignar' }}</span>
                                </div>
                                <div class="ticket-card-actions d-flex flex-column align-items-stretch mt-2"
                                    style="gap:0.4em;">
                                    <a href="{{ route('tickets.show', $ticket) }}"
                                        class="ticket-pill-btn ticket-pill-btn-custom ticket-pill-btn-details flex-fill"
                                        aria-label="Ver detalles del ticket">
                                        <i class="fas fa-eye"></i>
                                        <span>Detalles</span>
                                    </a>
                                    @if(Auth::user()->isAdmin() || Auth::user()->isSuperadmin())
                                    <a href="{{ route('tickets.edit', $ticket) }}"
                                        class="ticket-pill-btn ticket-pill-btn-custom ticket-pill-btn-edit flex-fill"
                                        aria-label="Editar ticket">
                                        <i class="fas fa-edit"></i>
                                        <span>Editar</span>
                                    </a>
                                    <button type="button"
                                        class="ticket-pill-btn ticket-pill-btn-custom ticket-pill-btn-delete flex-fill"
                                        data-bs-toggle="modal" data-bs-target="#confirmDeleteModal"
                                        data-form="delete-ticket-mobile-{{ $ticket->id }}" aria-label="Eliminar ticket">
                                        <i class="fas fa-trash"></i>
                                        <span>Eliminar</span>
                                    </button>
                                    <form id="delete-ticket-mobile-{{ $ticket->id }}"
                                        action="{{ route('tickets.destroy', $ticket) }}" method="POST" class="d-none">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                    @endif
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
</div>

{{-- Modal de confirmación de eliminación --}}
<div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel"
    aria-hidden="true">
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


@push('scripts')
<script>
    let formToSubmit = null;
    document.addEventListener('DOMContentLoaded', function() {
        var confirmDeleteModal = document.getElementById('confirmDeleteModal');
        confirmDeleteModal.addEventListener('show.bs.modal', function(event) {
            var button = event.relatedTarget;
            var formId = button.getAttribute('data-form');
            formToSubmit = document.getElementById(formId);
        });
        document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
            if (formToSubmit) formToSubmit.submit();
        });
    });

    function updateTicketsList() {
        fetch(window.location.href, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const newList = doc.getElementById('tickets-list-container');
                if (newList) {
                    document.getElementById('tickets-list-container').innerHTML = newList.innerHTML;
                }
            });
    }
    setInterval(updateTicketsList, 60000);

    document.addEventListener('DOMContentLoaded', function() {
        const userSearch = document.getElementById('user-search');
        const userSuggestions = document.getElementById('user-suggestions');
        const userIdInput = document.getElementById('user-id');
        const clearUserFilter = document.getElementById('clear-user-filter');

        if (userSearch) {
            let timeout = null;
            userSearch.addEventListener('input', function() {
                const query = this.value.trim();
                // Si el usuario edita el texto manualmente, limpiar el user_id oculto
                userIdInput.value = '';
                if (timeout) clearTimeout(timeout);
                if (query.length < 2) {
                    userSuggestions.innerHTML = '';
                    userSuggestions.style.display = 'none';
                    return;
                }
                timeout = setTimeout(() => {
                    fetch(`/admin/users/search?query=${encodeURIComponent(query)}`)
                        .then(res => res.json())
                        .then(data => {
                            userSuggestions.innerHTML = '';
                            if (data.length > 0) {
                                data.forEach(user => {
                                    const item = document.createElement('a');
                                    item.href = '#';
                                    item.className = 'list-group-item list-group-item-action';
                                    item.textContent = `${user.name} (${user.email})`;
                                    item.addEventListener('click', function(e) {
                                        e.preventDefault();
                                        userSearch.value = `${user.name} (${user.email})`;
                                        userIdInput.value = user.id;
                                        userSuggestions.innerHTML = '';
                                        userSuggestions.style.display = 'none';
                                        clearUserFilter.style.display = 'inline-block';
                                        // Redirigir con filtro
                                        const url = new URL(window.location.href);
                                        url.searchParams.set('user_id', user.id);
                                        window.location.href = url.toString();
                                    });
                                    userSuggestions.appendChild(item);
                                });
                                userSuggestions.style.display = 'block';
                            } else {
                                userSuggestions.style.display = 'none';
                            }
                        });
                }, 250);
            });
            document.addEventListener('click', function(e) {
                if (!userSuggestions.contains(e.target) && e.target !== userSearch) {
                    userSuggestions.innerHTML = '';
                    userSuggestions.style.display = 'none';
                }
            });
            clearUserFilter.addEventListener('click', function() {
                // Limpiar todos los campos del formulario de filtros
                document.getElementById('ticket-filters-form').reset();
                userIdInput.value = '';
                // Redirigir a la ruta base de tickets (sin filtros)
                window.location.href = window.location.pathname;
            });
        }
    });
</script>
@endpush



@endsection