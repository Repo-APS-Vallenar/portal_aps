@extends('layouts.app')

@section('content')
    <div class="container">
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <div class="row">
            <div class="col-md-8">
                <!-- Detalles del Ticket -->
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h2 class="mb-0">Ticket #{{ $ticket->id }}</h2>
                        <div class="btn-group">
                            <div class="d-flex gap-2">
                                @if(Auth::user()->isAdmin())
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
                                style="background-color: {{ $ticket->status->color }}; color: {{ $ticket->status->color == '#FFD700' ? '#000' : '#fff' }}">
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

                                    <!-- Botones para editar y eliminar (solo si es admin o el autor del comentario) -->
                                    @if(Auth::user()->isAdmin() || Auth::id() === $comment->user_id)
                                        <div class="mt-2">
                                            <button class="btn btn-sm btn-outline-secondary me-1"
                                                onclick="editComment({{ $comment->id }}, '{{ addslashes($comment->comment) }}')">
                                                Editar
                                            </button>

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

                            <dt class="col-sm-4">Asignado a:</dt>
                            <dd class="col-sm-8">
                                {{ $ticket->assignee ? $ticket->assignee->name : 'Sin asignar' }}
                            </dd>

                            <dt class="col-sm-4">Fecha límite:</dt>
                            <dd class="col-sm-8">
                                {{ $ticket->due_date ? $ticket->due_date->format('d/m/Y') : 'No establecida' }}
                            </dd>

                            <dt class="col-sm-4">Última actualización:</dt>
                            <dd class="col-sm-8">
                                {{ $ticket->updated_at->format('d/m/Y H:i') }}
                            </dd>
                        </dl>
                    </div>
                </div>

                <!-- Información del Hardware/Software -->
                @if($ticket->marca || $ticket->modelo || $ticket->numero_serie)
                    <div class="card mb-4">
                        <div class="card-header">
                            <h4 class="mb-0">Información del Equipo</h4>
                        </div>
                        <div class="card-body">
                            <dl class="row">
                            @if(Auth::user()->isAdmin())
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
                                @if($ticket->ubicacion)
                                    <dt class="col-sm-4">Ubicación:</dt>
                                    <dd class="col-sm-8">{{ $ticket->ubicacion }}</dd>
                                @endif

                                @if($ticket->usuario)
                                    <dt class="col-sm-4">Usuario:</dt>
                                    <dd class="col-sm-8">{{ $ticket->usuario }}</dd>
                                @endif

                                @if($ticket->ip_red_wifi)
                                    <dt class="col-sm-4">IP Red WiFi:</dt>
                                    <dd class="col-sm-8">{{ $ticket->ip_red_wifi }}</dd>
                                @endif
                            @if(Auth::user()->isAdmin())
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
                            @if(Auth::user()->isAdmin())
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
                                @if($ticket->comentarios)
                                    <dt class="col-sm-4">Comentarios:</dt>
                                    <dd class="col-sm-8">{{ $ticket->comentarios }}</dd>
                                @endif
                            </dl>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
    <script>
        function editComment(commentId, oldText) {
            const commentDiv = document.getElementById('comment-' + commentId);
            const commentText = document.getElementById('comment-text-' + commentId);

            // Reemplazar el texto por un formulario
            commentText.outerHTML = `
                    <form onsubmit="submitEditComment(event, ${commentId})" id="edit-form-${commentId}">
                        <div class="mb-2">
                            <textarea name="comment" class="form-control" rows="2" required>${oldText}</textarea>
                        </div>
                        <button type="submit" class="btn btn-sm btn-primary me-1">Guardar</button>
                        <button type="button" class="btn btn-sm btn-secondary" onclick="cancelEdit(${commentId}, \`${oldText}\`)">Cancelar</button>
                    </form>
                `;
        }

        function cancelEdit(commentId, originalText) {
            document.getElementById('edit-form-' + commentId).outerHTML =
                `<p class="mb-1" id="comment-text-${commentId}">${originalText}</p>`;
        }

        function submitEditComment(event, commentId) {
            event.preventDefault();
            const form = event.target;
            const commentValue = form.comment.value;
            const token = '{{ csrf_token() }}';

            fetch(`/tickets/comments/${commentId}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token
                },
                body: JSON.stringify({ comment: commentValue })
            })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        form.outerHTML = `<p class="mb-1" id="comment-text-${commentId}">${data.updated_comment}</p>`;
                    } else {
                        alert("Error al actualizar el comentario.");
                    }
                });
        }
    </script>
@endsection