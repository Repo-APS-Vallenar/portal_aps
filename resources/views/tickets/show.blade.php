@extends('layouts.app')

@section('content')
<div class="container">
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert" id="success-alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <script>
        setTimeout(function() {
            var alert = document.getElementById('success-alert');
            if (alert) {
                alert.classList.remove('show');
                alert.classList.add('fade');
                // Esperamos que la animación de desvanecimiento termine antes de eliminarla
                setTimeout(function() {
                    alert.remove();
                }, 150); // Espera el tiempo de la animación de desvanecimiento
            }
        }, 5000); // 5000 milisegundos (5 segundos) para cerrar la alerta
    </script>
    @endif


    <div class="row">
        <div class="col-md-8">
            <!-- Detalles del Ticket -->
            <div class="card mb-4 ticket-card" style="box-shadow: 0 2px 12px rgba(1,163,213,0.07), 0 1.5px 4px rgba(0,0,0,0.03); border-radius: 18px; border: 1px solid #e3e8ee; padding: 1.2rem 1.1rem 1.5rem 1.1rem;">
                <div class="card-header d-flex justify-content-between align-items-center" style="background: none; border: none; padding-bottom: 0;">
                    <h2 class="mb-0" style="font-size: 2rem; color: #222;">Ticket #{{ $ticket->id }}</h2>
                </div>
                <div class="card-body" style="padding-top: 0.7rem;">
                    <h3 style="color: #01a3d5; font-weight: 700;">{{ $ticket->title }}</h3>
                    <p class="text-muted" style="margin-bottom: 0.7rem;">
                        Creado por {{ $ticket->creator->name }} el {{ $ticket->created_at->format('d/m/Y H:i') }}
                    </p>
                    <div class="mb-3 d-flex flex-wrap gap-2 align-items-center">
                        <span class="badge" style="background-color: {{ $ticket->category->color }}; font-size: 1em; padding: 0.22rem 1.1rem; border-radius: 12px;">{{ $ticket->category->name }}</span>
                        <span class="badge" style="background-color: {{ $ticket->status->color }}; color: {{ $ticket->status->color == '#FFD700' ? '#000' : '#fff' }}; font-size: 1em; padding: 0.22rem 1.1rem; border-radius: 12px;">{{ $ticket->status->name }}</span>
                        <span class="badge bg-{{ $ticket->getPriorityColor() }}" style="font-size: 1em; padding: 0.22rem 1.1rem; border-radius: 12px;">{{ $ticket->getPriorityText() }}</span>
                    </div>
                    <div class="mb-3">
                        <h5 style="color: #222; font-weight: 600;">Descripción:</h5>
                        <p style="color: #444;">{{ $ticket->description }}</p>
                    </div>
                    <div class="ticket-card-actions d-flex flex-row gap-2 align-items-center flex-wrap flex-md-nowrap justify-content-end mt-3" style="margin-left: 0;">
                        @if(Auth::user()->isAdmin() || Auth::user()->isSuperadmin())
                            <a href="{{ route('tickets.edit', $ticket) }}" class="ticket-pill-btn ticket-pill-edit btn-action-responsive" style="font-size:0.97em; min-width:80px; max-width:120px; padding:0.35em 1em;">
                                <i class="fas fa-edit"></i>
                                <span>Editar</span>
                            </a>
                            <button type="button" class="ticket-pill-btn ticket-pill-delete btn-action-responsive" style="font-size:0.97em; min-width:80px; max-width:120px; padding:0.35em 1em;" data-bs-toggle="modal" data-bs-target="#confirmDeleteModal" data-form="delete-ticket-show">
                                <i class="fas fa-trash"></i>
                                <span>Eliminar</span>
                            </button>
                            <form id="delete-ticket-show" action="{{ route('tickets.destroy', $ticket) }}" method="POST" class="d-none">
                                @csrf
                                @method('DELETE')
                            </form>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Comentarios -->
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">Comentarios</h4>
                </div>
                <div class="card-body">
                    @if($ticket->comments->isEmpty())
                    <p class="text-muted">No hay comentarios aún.</p>
                    @else
                    @foreach($ticket->comments as $comment)
                    @if(!$comment->is_internal || (Auth::check() && Auth::user()->isAdmin() || Auth::user()->isSuperadmin()))
                    <div class="comment mb-4" id="comment-{{ $comment->id }}">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="mb-1">{{ $comment->user->name }}</h6>
                            <small class="text-muted">{{ $comment->created_at->format('d/m/Y H:i') }}</small>
                        </div>

                        <!-- Texto del comentario -->
                        <p class="mb-1" id="comment-text-{{ $comment->id }}">{{ $comment->comment }}</p>

                        @if($comment->is_internal)
                        <span class="badge bg-warning">Interno</span>
                        @endif

                        <!-- Botones para eliminar (solo si es admin o el autor del comentario) -->
                        @if(Auth::user()->isAdmin() || Auth::id() === $comment->user_id)
                        <div class="mt-2">
                            <form action="{{ route('tickets.deleteComment', [$ticket->id, $comment->id]) }}"
                                method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger"
                                    onclick="return confirm('¿Estás seguro de eliminar este comentario?')">
                                    Eliminar
                                </button>
                            </form>
                        </div>
                        @endif
                    </div>
                    @endif
                    @endforeach

                    @endif

                    <!-- Formulario para nuevo comentario -->
                    <form action="{{ route('tickets.addComment', $ticket) }}" method="POST" class="mt-4">
                        @csrf
                        <div class="mb-3">
                            <label for="content" class="form-label">Nuevo Comentario</label>
                            <textarea class="form-control @error('comment') is-invalid @enderror" id="comment"
                                name="comment" rows="3" required></textarea>
                            @error('comment')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_internal" name="is_internal"
                                    value="1">
                                <label class="form-check-label" for="is_internal">
                                    Comentario interno (solo visible para el staff)
                                </label>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Agregar Comentario</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <!-- Información Adicional -->
            <div class="card mb-4">
                <div class="card-header">
                    <h4 class="mb-0">Información Adicional</h4>
                </div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-4">Estado:</dt>
                        <dd class="col-sm-8">
                            <span class="badge"
                                style="background-color: {{ $ticket->status->color }}; color: {{ $ticket->status->color == '#FFD700' ? '#000' : '#fff' }}">
                                {{ $ticket->status->name }}
                            </span>
                        </dd>

                        <dt class="col-sm-4">Contacto:</dt>
                        <dd class="col-sm-8">
                            {{ $ticket->contact_email ?? 'No asignado' }}
                            {{ $ticket->contact_phone ? ' - ' . $ticket->contact_phone : '' }}
                        </dd>

                        <dt class="col-sm-4">Ubicación:</dt>
                        <dd class="col-sm-8">{{ $ticket->location->name ?? 'No asignada' }}</dd>

                        <dt class="col-sm-4">Asignado a:</dt>
                        <dd class="col-sm-8">
                            {{ $ticket->assignee->name ?? 'No asignado' }}
                        </dd>


                        <dt class="col-sm-4">Última actualización:</dt>
                        <dd class="col-sm-8">
                            {{ $ticket->updated_at->setTimezone('America/Santiago')->format('d/m/Y H:i') }}
                        </dd>


                        <dt class="col-sm-4">Solución Aplicada:</dt>
                        <dd class="col-sm-8">{{ $ticket->comentarios ?? 'No hay solución aplicada aún' }}</dd>
                    </dl>
                </div>
            </div>

            <!-- Información del Hardware/Software -->

            <div class="card mb-4">
                <div class="card-header">
                    <h4 class="mb-0">Información del Equipo</h4>
                </div>
                <div class="card-body">
                    <dl class="row">
                        @if(Auth::user()->isAdmin() || Auth::user()->isSuperadmin())
                        @if($ticket->marca)
                        <dt class="col-sm-4">Marca:</dt>
                        <dd class="col-sm-8">{{ $ticket->marca }}</dd>
                        @endif

                        @if($ticket->modelo)
                        <dt class="col-sm-4">Modelo:</dt>
                        <dd class="col-sm-8">{{ $ticket->modelo }}</dd>
                        @endif

                        @if($ticket->numero_serie)
                        <dt class="col-sm-4">Número de Serie:</dt>
                        <dd class="col-sm-8">{{ $ticket->numero_serie }}</dd>
                        @endif
                        @endif

                        @if($ticket->usuario)
                        <dt class="col-sm-4">Usuario:</dt>
                        <dd class="col-sm-8">{{ $ticket->usuario }}</dd>
                        @endif

                        @if($ticket->ip_red_wifi)
                        <dt class="col-sm-4">IP Red WiFi:</dt>
                        <dd class="col-sm-8">{{ $ticket->ip_red_wifi }}</dd>
                        @endif
                        @if(Auth::user()->isAdmin() || Auth::user()->isSuperadmin())
                        @if($ticket->cpu)
                        <dt class="col-sm-4">CPU:</dt>
                        <dd class="col-sm-8">{{ $ticket->cpu }}</dd>
                        @endif

                        @if($ticket->ram)
                        <dt class="col-sm-4">RAM:</dt>
                        <dd class="col-sm-8">{{ $ticket->ram }}</dd>
                        @endif

                        @if($ticket->capacidad_almacenamiento)
                        <dt class="col-sm-4">Almacenamiento:</dt>
                        <dd class="col-sm-8">{{ $ticket->capacidad_almacenamiento }}</dd>
                        @endif

                        @if($ticket->tarjeta_video)
                        <dt class="col-sm-4">Tarjeta de Video:</dt>
                        <dd class="col-sm-8">{{ $ticket->tarjeta_video }}</dd>
                        @endif
                        @endif
                        @if($ticket->id_anydesk)
                        <dt class="col-sm-4">ID AnyDesk:</dt>
                        <dd class="col-sm-8">{{ $ticket->id_anydesk }}</dd>
                        @endif
                        @if(Auth::user()->isAdmin() || Auth::user()->isSuperadmin())
                        @if($ticket->version_windows)
                        <dt class="col-sm-4">Versión Windows:</dt>
                        <dd class="col-sm-8">{{ $ticket->version_windows }}</dd>
                        @endif

                        @if($ticket->version_office)
                        <dt class="col-sm-4">Versión Office:</dt>
                        <dd class="col-sm-8">{{ $ticket->version_office }}</dd>
                        @endif

                        @if($ticket->fecha_instalacion)
                        <dt class="col-sm-4">Fecha de Instalación:</dt>
                        <dd class="col-sm-8">{{ $ticket->fecha_instalacion->format('d/m/Y') }}</dd>
                        @endif
                        @endif
                    </dl>
                </div>
            </div>

        </div>
    </div>
</div>

{{-- Modal de confirmación de eliminación de ticket --}}
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
@endsection