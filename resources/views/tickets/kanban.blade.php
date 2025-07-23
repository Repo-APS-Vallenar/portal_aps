@extends('layouts.app')

@section('title', 'Tickets - Vista Kanban')

@push('styles')
<style>
    .kanban-board {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 1rem;
        padding: 1rem;
        overflow-x: auto;
    }

    .kanban-column {
        background: #f8f9fa;
        border-radius: 12px;
        padding: 1rem;
        min-height: 500px;
        border: 2px solid #e9ecef;
    }

    .kanban-column.drag-over {
        border-color: #007bff;
        background-color: #e3f2fd;
    }

    .kanban-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
        padding-bottom: 0.5rem;
        border-bottom: 2px solid #dee2e6;
    }

    .kanban-title {
        font-weight: 600;
        font-size: 1.1rem;
        color: #495057;
    }

    .kanban-count {
        background: #6c757d;
        color: white;
        border-radius: 12px;
        padding: 0.25rem 0.5rem;
        font-size: 0.8rem;
        min-width: 20px;
        text-align: center;
    }

    .kanban-ticket {
        background: white;
        border-radius: 8px;
        padding: 1rem;
        margin-bottom: 0.75rem;
        cursor: move;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        border-left: 4px solid #dee2e6;
        transition: all 0.2s ease;
    }

    .kanban-ticket:hover {
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        transform: translateY(-2px);
    }

    .kanban-ticket.dragging {
        opacity: 0.5;
        transform: rotate(5deg);
    }

    .kanban-ticket.priority-baja {
        border-left-color: #28a745;
    }

    .kanban-ticket.priority-media {
        border-left-color: #ffc107;
    }

    .kanban-ticket.priority-alta {
        border-left-color: #fd7e14;
    }

    .kanban-ticket.priority-urgente {
        border-left-color: #dc3545;
        animation: pulse-urgent 2s infinite;
    }

    @keyframes pulse-urgent {
        0%, 100% { box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        50% { box-shadow: 0 4px 12px rgba(220, 53, 69, 0.3); }
    }

    .ticket-title {
        font-weight: 600;
        color: #212529;
        margin-bottom: 0.5rem;
        font-size: 0.9rem;
        line-height: 1.3;
    }

    .ticket-meta {
        font-size: 0.75rem;
        color: #6c757d;
        margin-bottom: 0.5rem;
    }

    .ticket-priority {
        display: inline-block;
        padding: 0.2rem 0.5rem;
        border-radius: 12px;
        font-size: 0.7rem;
        font-weight: 600;
        text-transform: uppercase;
    }

    .priority-baja { background: #d4edda; color: #155724; }
    .priority-media { background: #fff3cd; color: #856404; }
    .priority-alta { background: #f8d7da; color: #721c24; }
    .priority-urgente { background: #f5c6cb; color: #721c24; }

    .ticket-assignee {
        font-size: 0.75rem;
        color: #495057;
        margin-top: 0.5rem;
    }

    .empty-state-message {
        transition: opacity 0.3s ease, transform 0.3s ease;
        animation: fadeIn 0.3s ease;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .view-toggle {
        margin-bottom: 1rem;
    }

    .view-toggle .btn-group {
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

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
        
        .view-toggle .btn-group {
            flex-direction: column;
            width: 100%;
        }
        
        .view-toggle .btn-group .btn {
            border-radius: 6px !important;
            margin-bottom: 0.25rem;
        }
    }

    @media (max-width: 768px) {
        .kanban-board {
            grid-template-columns: 1fr;
            gap: 0.5rem;
            padding: 0.5rem;
        }
        
        .kanban-column {
            min-height: 300px;
        }
    }

    /* Estilos para modo oscuro */
    [data-theme="dark"] .kanban-column {
        background: var(--surface-color);
        border-color: var(--border-color);
    }

    [data-theme="dark"] .kanban-column.drag-over {
        border-color: var(--accent-color);
        background-color: rgba(59, 130, 246, 0.1);
    }

    [data-theme="dark"] .kanban-title {
        color: var(--text-color);
    }

    [data-theme="dark"] .kanban-count {
        background: var(--bg-secondary);
        color: var(--text-color);
    }

    [data-theme="dark"] .kanban-ticket {
        background: var(--card-bg);
        color: var(--text-color);
        box-shadow: 0 2px 4px rgba(0,0,0,0.3);
    }

    [data-theme="dark"] .kanban-ticket:hover {
        box-shadow: 0 4px 12px rgba(0,0,0,0.4);
    }

    [data-theme="dark"] .ticket-title {
        color: var(--text-color);
    }

    [data-theme="dark"] .ticket-meta {
        color: var(--text-secondary);
    }

    [data-theme="dark"] .ticket-assignee {
        color: var(--text-color);
    }

    [data-theme="dark"] .empty-state-message {
        color: var(--text-secondary);
    }

    /* Prioridades en modo oscuro - mantener colores distintivos */
    [data-theme="dark"] .kanban-ticket.priority-baja {
        border-left-color: #10b981;
    }

    [data-theme="dark"] .kanban-ticket.priority-media {
        border-left-color: #f59e0b;
    }

    [data-theme="dark"] .kanban-ticket.priority-alta {
        border-left-color: #f97316;
    }

    [data-theme="dark"] .kanban-ticket.priority-urgente {
        border-left-color: #ef4444;
    }

    [data-theme="dark"] .priority-baja { 
        background: rgba(16, 185, 129, 0.2); 
        color: #10b981; 
    }
    
    [data-theme="dark"] .priority-media { 
        background: rgba(245, 158, 11, 0.2); 
        color: #f59e0b; 
    }
    
    [data-theme="dark"] .priority-alta { 
        background: rgba(249, 115, 22, 0.2); 
        color: #f97316; 
    }
    
    [data-theme="dark"] .priority-urgente { 
        background: rgba(239, 68, 68, 0.2); 
        color: #ef4444; 
    }

    /* Animación de pulse urgente en modo oscuro */
    [data-theme="dark"] .kanban-ticket.priority-urgente {
        animation: pulse-urgent-dark 2s infinite;
    }

    @keyframes pulse-urgent-dark {
        0%, 100% { box-shadow: 0 2px 4px rgba(0,0,0,0.3); }
        50% { box-shadow: 0 4px 12px rgba(239, 68, 68, 0.4); }
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
                    <h2 class="mb-0">Tickets - Vista Kanban</h2>
                    <div class="d-flex gap-2 flex-wrap">
                        <!-- Toggle de vista -->
                        <div class="view-toggle">
                            <div class="btn-group" role="group">
                                <a href="{{ route('tickets.index') }}" class="btn btn-outline-primary">
                                    <i class="fas fa-list"></i> Lista
                                </a>
                                <a href="{{ route('tickets.kanban') }}" class="btn btn-primary">
                                    <i class="fas fa-columns"></i> Kanban
                                </a>
                            </div>
                        </div>
                        <a href="{{ route('tickets.create') }}" class="btn btn-new-ticket">
                            <i class="fas fa-plus"></i> Nuevo Ticket
                        </a>
                    </div>
                </div>
                
                <div class="card-body p-0">
                    @if(session('success'))
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            showAlert(@json(session('success')), 'success', document.querySelector('.container-fluid'), 5000);
                        });
                    </script>
                    @endif

                    @if (session('error'))
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            showAlert(@json(session('error')), 'danger', document.querySelector('.container-fluid'), 5000);
                        });
                    </script>
                    @endif

                    <!-- Filtros simplificados para Kanban -->
                    @auth
                    @if(Auth::user()->role === 'admin' || Auth::user()->role === 'superadmin')
                    <div class="p-3 border-bottom">
                        <form id="kanban-filters-form" method="GET" action="{{ route('tickets.kanban') }}">
                            <div class="row g-2 align-items-end">
                                <div class="col-md-3">
                                    <label for="user_name" class="form-label small">Usuario</label>
                                    <input type="text" name="user_name" class="form-control form-control-sm" placeholder="Filtrar por usuario..." value="{{ request('user_name') }}">
                                </div>
                                <div class="col-md-2">
                                    <label for="priority" class="form-label small">Prioridad</label>
                                    <select class="form-select form-select-sm" name="priority">
                                        <option value="">Todas</option>
                                        <option value="baja" @if(request('priority') == 'baja') selected @endif>Baja</option>
                                        <option value="media" @if(request('priority') == 'media') selected @endif>Media</option>
                                        <option value="alta" @if(request('priority') == 'alta') selected @endif>Alta</option>
                                        <option value="urgente" @if(request('priority') == 'urgente') selected @endif>Urgente</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label for="date_from" class="form-label small">Desde</label>
                                    <input type="date" name="date_from" class="form-control form-control-sm" value="{{ request('date_from') }}">
                                </div>
                                <div class="col-md-2">
                                    <label for="date_to" class="form-label small">Hasta</label>
                                    <input type="date" name="date_to" class="form-control form-control-sm" value="{{ request('date_to') }}">
                                </div>
                                <div class="col-md-3">
                                    <button type="submit" class="btn btn-primary btn-sm">
                                        <i class="fas fa-filter"></i> Filtrar
                                    </button>
                                    <a href="{{ route('tickets.kanban') }}" class="btn btn-outline-secondary btn-sm">
                                        <i class="fas fa-times"></i> Limpiar
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                    @endif
                    @endauth

                    <!-- Tablero Kanban -->
                    <div id="kanban-container">
                        @include('tickets.partials.kanban-board')
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    initializeKanban();
    updateEmptyMessages(); // Inicializar mensajes vacíos al cargar
});

function initializeKanban() {
    const tickets = document.querySelectorAll('.kanban-ticket');
    const columns = document.querySelectorAll('.kanban-column');

    // Hacer tickets draggables
    tickets.forEach(ticket => {
        ticket.draggable = true;
        
        // Manejar click para navegar al ticket
        ticket.addEventListener('click', function(e) {
            // Solo navegar si no estamos arrastrando
            if (!this.classList.contains('dragging')) {
                const url = this.dataset.ticketUrl;
                if (url) {
                    window.location.href = url;
                }
            }
        });
        
        ticket.addEventListener('dragstart', function(e) {
            this.classList.add('dragging');
            e.dataTransfer.setData('text/plain', this.dataset.ticketId);
        });
        
        ticket.addEventListener('dragend', function(e) {
            this.classList.remove('dragging');
        });
    });

    // Configurar columnas como drop zones
    columns.forEach(column => {
        column.addEventListener('dragover', function(e) {
            e.preventDefault();
            this.classList.add('drag-over');
        });
        
        column.addEventListener('dragleave', function(e) {
            this.classList.remove('drag-over');
        });
        
        column.addEventListener('drop', function(e) {
            e.preventDefault();
            this.classList.remove('drag-over');
            
            const ticketId = e.dataTransfer.getData('text/plain');
            const statusId = this.dataset.statusId;
            const ticketElement = document.querySelector(`[data-ticket-id="${ticketId}"]`);
            
            if (ticketElement) {
                // Obtener la columna de origen
                const sourceColumn = ticketElement.closest('.kanban-column');
                
                // Mover visualmente primero
                const ticketsContainer = this.querySelector('.kanban-tickets');
                ticketsContainer.appendChild(ticketElement);
                
                // Actualizar contadores y mensajes vacíos
                updateColumnCounts();
                updateEmptyMessages();
                
                // Enviar actualización al servidor
                updateTicketStatus(ticketId, statusId);
            }
        });
    });
}

function updateTicketStatus(ticketId, statusId) {
    fetch(`/tickets/${ticketId}/status`, {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            status_id: statusId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert(data.message, 'success', document.querySelector('.container-fluid'), 3000);
        } else {
            showAlert('Error al actualizar el ticket', 'danger', document.querySelector('.container-fluid'), 3000);
            // Recargar la página si hay error
            setTimeout(() => location.reload(), 2000);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('Error de conexión', 'danger', document.querySelector('.container-fluid'), 3000);
        // Recargar la página si hay error
        setTimeout(() => location.reload(), 2000);
    });
}

function updateColumnCounts() {
    document.querySelectorAll('.kanban-column').forEach(column => {
        const count = column.querySelectorAll('.kanban-ticket').length;
        const countElement = column.querySelector('.kanban-count');
        if (countElement) {
            countElement.textContent = count;
        }
    });
}

function updateEmptyMessages() {
    document.querySelectorAll('.kanban-column').forEach(column => {
        const ticketsContainer = column.querySelector('.kanban-tickets');
        const tickets = ticketsContainer.querySelectorAll('.kanban-ticket');
        let emptyMessage = ticketsContainer.querySelector('.empty-state-message');
        
        if (tickets.length === 0) {
            // No hay tickets, mostrar mensaje vacío si no existe
            if (!emptyMessage) {
                emptyMessage = document.createElement('div');
                emptyMessage.className = 'text-center text-muted py-4 empty-state-message';
                emptyMessage.innerHTML = `
                    <i class="fas fa-inbox fa-2x mb-2"></i>
                    <p class="mb-0">No hay tickets en este estado</p>
                `;
                ticketsContainer.appendChild(emptyMessage);
            }
        } else {
            // Hay tickets, remover mensaje vacío si existe
            if (emptyMessage) {
                emptyMessage.remove();
            }
        }
    });
}

// Actualizar los draggables cuando se carga contenido dinámico
function refreshKanban() {
    initializeKanban();
    updateEmptyMessages();
}
</script>
@endpush
