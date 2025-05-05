<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h2 class="mb-0">Ticket #{{ $ticket->id }}</h2>
        <div class="btn-group">
            <div class="d-flex gap-2">
                @if(Auth::user()->isAdmin() || Auth::user()->isSuperadmin())
                <a href="{{ route('tickets.edit', $ticket) }}" class="btn btn-warning me-2">
                    <i class="fas fa-edit"></i> Editar
                </a>

                <form action="{{ route('tickets.destroy', $ticket) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger"
                        onclick="return confirm('¿Estás seguro de eliminar este ticket?')">
                        <i class="fas fa-trash"></i> Eliminar
                    </button>
                </form>
                @endif
            </div>

        </div>
    </div>
    <div class="card-body">
        <h3>{{ $ticket->title }}</h3>
        <p class="text-muted">
            Creado por {{ $ticket->creator->name }} el {{ $ticket->created_at->format('d/m/Y H:i') }}
        </p>
        <div class="mb-3">
            <span class="badge" style="background-color: {{ $ticket->category->color }}">
                {{ $ticket->category->name }}
            </span>
            <span class="badge"
                style="background-color: {{ $ticket->status->color }}; color: {{ $ticket->status->color == '#FFD700' ? '#000' : '#fff' }};">
                {{ $ticket->status->name }}
            </span>
            <span class="badge bg-{{ $ticket->getPriorityColor() }}">
                {{ $ticket->getPriorityText() }}
            </span>
        </div>
        <div class="mb-3">
            <h5>Descripción:</h5>
            <p>{{ $ticket->description }}</p>
        </div>
    </div>
</div>