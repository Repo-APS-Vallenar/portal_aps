<div id="tickets-list-container">
    @if($tickets->isEmpty())
        <div class="alert alert-info">
            No hay tickets disponibles.
        </div>
    @endif
    <div class="table-responsive d-none d-md-block">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Usuario</th>
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
</div> 