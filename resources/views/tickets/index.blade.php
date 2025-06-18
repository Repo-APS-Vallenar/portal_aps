@extends('layouts.app')

@section('title', 'Tickets')

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
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="user-search" class="form-label">Buscar usuario</label>
                                <input type="text" id="user-search" class="form-control" placeholder="Nombre o email del usuario...">
                                <input type="hidden" id="user-id" name="user_id" value="{{ request('user_id') }}">
                                <div id="user-suggestions" class="list-group position-absolute w-100" style="z-index: 1000;"></div>
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button id="clear-user-filter" class="btn btn-secondary ms-2" style="display:none;">Limpiar filtro</button>
                            </div>
                        </div>
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
                                                    'title' => 'Usuario',
                                                    'category_id' => 'Categoría',
                                                    'status_id' => 'Estado',
                                                    'priority' => 'Prioridad',
                                                    'created_by' => 'Creado por',
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
                                            <span>{{ $ticket->title }}</span></div>
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
                                            <span>{{ $ticket->creator->name }}</span></div>
                                        <div class="mb-2"><span class="ticket-card-label">Asignado a:</span>
                                                <span>{{ $ticket->assignedTo ? $ticket->assignedTo->name : 'Sin asignar' }}</span></div>
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

            document.addEventListener('DOMContentLoaded', function () {
                const userSearch = document.getElementById('user-search');
                const userSuggestions = document.getElementById('user-suggestions');
                const userIdInput = document.getElementById('user-id');
                const clearUserFilter = document.getElementById('clear-user-filter');

                if (userSearch) {
                    let timeout = null;
                    userSearch.addEventListener('input', function () {
                        const query = this.value.trim();
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
                                            item.addEventListener('click', function (e) {
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
                    document.addEventListener('click', function (e) {
                        if (!userSuggestions.contains(e.target) && e.target !== userSearch) {
                            userSuggestions.innerHTML = '';
                            userSuggestions.style.display = 'none';
                        }
                    });
                    clearUserFilter.addEventListener('click', function () {
                        userSearch.value = '';
                        userIdInput.value = '';
                        clearUserFilter.style.display = 'none';
                        const url = new URL(window.location.href);
                        url.searchParams.delete('user_id');
                        window.location.href = url.toString();
                    });
                }
            });
        </script>
    @endpush

  

@endsection