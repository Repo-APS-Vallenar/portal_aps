<div class="kanban-board">
    @foreach($statuses as $status)
    <div class="kanban-column" data-status-id="{{ $status->id }}">
        <div class="kanban-header">
            <div class="kanban-title">{{ $status->name }}</div>
            <div class="kanban-count">{{ $ticketsByStatus[$status->id]->count() }}</div>
        </div>
        
        <div class="kanban-tickets">
            @forelse($ticketsByStatus[$status->id] as $ticket)
            <div class="kanban-ticket priority-{{ $ticket->priority }}" 
                 data-ticket-id="{{ $ticket->id }}"
                 data-ticket-url="{{ route('tickets.show', $ticket) }}">
                
                <div class="ticket-title">
                    #{{ $ticket->id }} - {{ Str::limit($ticket->title, 50) }}
                </div>
                
                <div class="ticket-meta">
                    <i class="fas fa-tag"></i> {{ $ticket->category->name ?? 'Sin categoría' }}
                    @if($ticket->location)
                    <br><i class="fas fa-map-marker-alt"></i> {{ $ticket->location->name }}
                    @endif
                </div>
                
                <div class="d-flex justify-content-between align-items-center">
                    <span class="ticket-priority priority-{{ $ticket->priority }}">
                        {{ ucfirst($ticket->priority) }}
                    </span>
                    <small class="text-muted">
                        {{ $ticket->created_at->diffForHumans() }}
                    </small>
                </div>
                
                @if($ticket->assignedTo)
                <div class="ticket-assignee">
                    <i class="fas fa-user"></i> {{ $ticket->assignedTo->name }}
                </div>
                @else
                <div class="ticket-assignee text-muted">
                    <i class="fas fa-user-slash"></i> Sin asignar
                </div>
                @endif
                
                <div class="ticket-meta mt-2">
                    <small>
                        <i class="fas fa-user-plus"></i> {{ $ticket->creator->name }}
                    </small>
                </div>
            </div>
            @empty
            <div class="text-center text-muted py-4 empty-state-message">
                <i class="fas fa-inbox fa-2x mb-2"></i>
                <p class="mb-0">No hay tickets en este estado</p>
            </div>
            @endforelse
        </div>
    </div>
    @endforeach
</div>
