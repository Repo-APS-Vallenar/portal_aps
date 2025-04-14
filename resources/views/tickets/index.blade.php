@extends('layouts.app')



@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h2 class="mb-0">Tickets</h2>
                        <a href="{{ route('tickets.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Nuevo Ticket
                        </a>
                    </div>
                    <input type="hidden" name="user_id" value="{{ auth()->id() }}">
                    <div class="card-body">
                        @if (session('success'))
                            <div class="alert alert-success" role="alert">
                                {{ session('success') }}
                            </div>
                        @endif

                        @if (session('error'))
                            <div class="alert alert-danger" role="alert">
                                {{ session('error') }}
                            </div>
                        @endif

                        @if($tickets->isEmpty())
                            <div class="alert alert-info">
                                No hay tickets disponibles.
                            </div>
                        @else

                            <div class="table-responsive">
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
                                                            <!-- Ver -->
                                                            <a href="{{ route('tickets.show', $ticket) }}"
                                                                class="btn btn-outline-success btn-sm">
                                                                <i class="fas fa-eye me-1"></i> Detalles
                                                            </a>

                                                            @if(Auth::user()->isAdmin())
                                                                <!-- Editar -->
                                                                <a href="{{ route('tickets.edit', $ticket) }}"
                                                                    class="btn btn-outline-warning btn-sm">
                                                                    <i class="fas fa-edit me-1"></i> Editar
                                                                </a>

                                                                <!-- Botón Eliminar -->
                                                                <button type="button" class="btn btn-outline-danger btn-sm"
                                                                    onclick="if(confirm('¿Estás seguro de que deseas eliminar este ticket?')) { document.getElementById('delete-ticket-{{ $ticket->id }}').submit(); }">
                                                                    <i class="fas fa-trash me-1"></i> Eliminar
                                                                </button>
                                                            @endif
                                                        </div>

                                                        <!-- Formulario oculto para eliminar -->
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
                            <div class="d-flex justify-content-center mt-4">
                                {{ $tickets->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection