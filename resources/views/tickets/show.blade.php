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
                <div class="card-body" id="comments-card-body">
                    <div id="comments-list">
                        @forelse($ticket->comments as $comment)
                            @include('tickets.partials.comment', ['comment' => $comment, 'ticket' => $ticket])
                        @empty
                            <p class="text-muted no-comments-msg">No hay comentarios aún.</p>
                        @endforelse
                    </div>

                    <!-- Formulario para nuevo comentario -->
                    <form action="{{ route('tickets.addComment', $ticket) }}" method="POST" class="mt-4" id="commentForm">
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
                        <button type="submit" class="btn btn-primary" id="submitComment">Agregar Comentario</button>
                    </form>
                </div>
            </div>

            <!-- Visualización de documentos adjuntos -->
            <div class="card mt-4">
                <div class="card-header">
                    <h4 class="mb-0">Documentos Adjuntos</h4>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        @foreach($ticket->documents as $document)
                            <div class="col-12 col-md-6 col-lg-4">
                                <div class="doc-card card h-100 shadow-sm border-0 d-flex flex-column align-items-center justify-content-between">
                                    <div class="card-body w-100 d-flex flex-column align-items-center justify-content-between p-0 pb-3">
                                        @if(Str::startsWith($document->file_type, 'image/'))
                                            <img src="{{ asset('storage/' . $document->file_path) }}" alt="Imagen adjunta" class="img-fluid doc-img-thumb mb-3 mt-3" style="max-width: 180px; max-height: 140px; border-radius: 16px; border: 1.5px solid #e3e8ee; cursor:pointer; box-shadow: 0 2px 8px rgba(33,150,243,0.07);" data-bs-toggle="modal" data-bs-target="#imageModal" data-img-src="{{ asset('storage/' . $document->file_path) }}">
                                        @else
                                            <i class="fas fa-file fa-3x text-secondary mb-3 mt-3"></i>
                                        @endif
                                        <div class="w-100 text-center mb-2">
                                            <strong>{{ $document->file_name }}</strong>
                                            @if($document->description)
                                                <div class="text-muted small mt-1">{{ $document->description }}</div>
                                            @endif
                                        </div>
                                        <div class="pill-btn-group mt-auto mb-2 justify-content-center w-100 flex-row flex-nowrap">
                                            <a href="{{ route('tickets.documents.download', $document) }}" class="pill-btn pill-btn-download text-center" title="Descargar">
                                                <i class="fas fa-download"></i>
                                            </a>
                                            @if(Auth::user()->isAdmin() || Auth::user()->isSuperadmin())
                                                <form action="{{ route('tickets.documents.destroy', $document) }}" method="POST" class="d-inline m-0 p-0 delete-document-form">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="pill-btn pill-btn-delete btn-delete-document text-center" title="Eliminar">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
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
                            {{ $ticket->assignedTo->name ?? 'No asignado' }}
                        </dd>


                        <dt class="col-sm-4">Última actualización:</dt>
                        <dd class="col-sm-8">
                            {{ $ticket->updated_at->setTimezone('America/Santiago')->format('d/m/Y H:i') }}
                        </dd>


                        <dt class="col-sm-4">Solución Aplicada:</dt>
                        <dd class="col-sm-8">{{ $ticket->solucion_aplicada ?? 'No hay solución aplicada aún' }}</dd>
                    </dl>
                </div>
            </div>

            {{-- Formulario de subida de documentos adjuntos --}}
            @include('tickets.partials.document-upload', ['ticket' => $ticket])

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

{{-- Modal de confirmación de eliminación de comentario --}}
<div class="modal fade" id="confirmDeleteCommentModal" tabindex="-1" aria-labelledby="confirmDeleteCommentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="confirmDeleteCommentModalLabel">Confirmar eliminación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                ¿Estás seguro de que deseas eliminar este comentario?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteCommentBtn">Eliminar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para mostrar imagen en grande -->
<div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="imageModalLabel">Vista de Imagen</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body text-center">
                <img id="modalImage" src="" alt="Imagen adjunta" style="max-width: 100%; max-height: 70vh; border-radius: 10px; box-shadow: 0 2px 12px rgba(1,163,213,0.07);">
            </div>
        </div>
    </div>
</div>

<!-- Modal de confirmación para eliminar documento -->
<div class="modal fade" id="confirmDeleteDocumentModal" tabindex="-1" aria-labelledby="confirmDeleteDocumentLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title" id="confirmDeleteDocumentLabel">Confirmar eliminación</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        ¿Estás seguro de que deseas eliminar este documento?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-danger" id="confirmDeleteDocumentBtn">Eliminar</button>
      </div>
    </div>
  </div>
</div>

@push('scripts')
<script>
    let formToSubmit = null;
    let commentToDelete = null;
    let ticketId = null;
    let lastCommentId = null;

    window.authUserName = @json(Auth::user()->name);
    window.authUserRole = @json(Auth::user()->role);
    window.ticketId = {{ $ticket->id }};

    // Función para actualizar los comentarios
    function updateComments() {
        let commentsContainer = document.getElementById('comments-card-body');
        let commentsList = document.getElementById('comments-list');
        if (!commentsContainer) return;
        fetch(`/tickets/{{ $ticket->id }}/comments`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (!commentsList) {
                commentsList = document.createElement('div');
                commentsList.id = 'comments-list';
                let commentForm = document.getElementById('commentForm');
                if (commentForm && commentForm.parentNode) {
                    commentForm.parentNode.insertBefore(commentsList, commentForm);
                } else {
                    commentsContainer.appendChild(commentsList);
                }
            }
            if (data.success) {
                commentsList.innerHTML = data.html;
                if (!data.html.trim()) {
                    commentsList.innerHTML = '<p class="text-muted no-comments-msg">No hay comentarios aún.</p>';
                }
            }
        })
        .catch(error => console.error('Error al actualizar comentarios:', error));
    }

    document.addEventListener('DOMContentLoaded', function () {
        // Modal de eliminación de ticket
        var confirmDeleteModal = document.getElementById('confirmDeleteModal');
        confirmDeleteModal.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget;
            var formId = button.getAttribute('data-form');
            formToSubmit = document.getElementById(formId);
        });
        document.getElementById('confirmDeleteBtn').addEventListener('click', function () {
            if (formToSubmit) formToSubmit.submit();
        });

        // Modal de eliminación de comentario
        var confirmDeleteCommentModal = document.getElementById('confirmDeleteCommentModal');
        confirmDeleteCommentModal.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget;
            commentToDelete = button.getAttribute('data-comment-id');
            ticketId = button.getAttribute('data-ticket-id');
        });

        document.getElementById('confirmDeleteCommentBtn').addEventListener('click', function () {
            if (commentToDelete && ticketId) {
                // Eliminar el comentario del DOM inmediatamente
                let commentsContainer = document.getElementById('comments-card-body');
                if (!commentsContainer) return;
                const commentElement = document.querySelector(`[data-comment-id="${commentToDelete}"]`);
                if (commentElement) {
                    commentElement.remove();
                }
                // Cerrar el modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('confirmDeleteCommentModal'));
                modal.hide();

                // Crear el formulario dinámicamente
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/tickets/${ticketId}/comments/${commentToDelete}`;
                // Agregar el token CSRF
                const csrfInput = document.createElement('input');
                csrfInput.type = 'hidden';
                csrfInput.name = '_token';
                csrfInput.value = document.querySelector('meta[name="csrf-token"]').content;
                form.appendChild(csrfInput);
                // Agregar el método DELETE
                const methodInput = document.createElement('input');
                methodInput.type = 'hidden';
                methodInput.name = '_method';
                methodInput.value = 'DELETE';
                form.appendChild(methodInput);
                // Enviar la petición AJAX
                fetch(form.action, {
                    method: 'POST',
                    body: new FormData(form),
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Mostrar mensaje de éxito
                        showAlert(data.message, 'danger', commentsContainer, 1500);
                        // Actualizar la lista completa de comentarios
                        updateComments();
                    } else {
                        throw new Error(data.message || 'Error al eliminar el comentario');
                    }
                })
                .catch(error => {
                    // Si hay error, recargar la lista de comentarios para restaurar el DOM
                    updateComments();
                    // Mostrar mensaje de error
                    showAlert(error.message || 'Error al eliminar el comentario. Por favor, intente nuevamente.', 'danger', commentsContainer, 2000);
                });
            }
        });

        // Actualizar comentarios cada 5 minutos como respaldo
        setInterval(updateComments, 300000);

        // --- Pusher tiempo real ---
        if (window.ticketId && window.Echo) {
            window.Echo.private('ticket.' + window.ticketId)
                .listen('.comment-added', (e) => {
                    updateComments();
                })
                .listen('.comment-deleted', (e) => {
                    updateComments();
                });
        }
    });

    document.addEventListener('DOMContentLoaded', function() {
        const commentForm = document.getElementById('commentForm');
        const commentTextarea = document.getElementById('comment');
        const submitButton = document.getElementById('submitComment');
        let commentsContainer = document.getElementById('comments-card-body');
        if (!commentsContainer) return;
        const commentsList = document.getElementById('comments-list');

        commentForm.addEventListener('submit', function(e) {
            e.preventDefault();

            // Eliminar TODOS los mensajes de "No hay comentarios" inmediatamente (antes de AJAX)
            let noCommentsMsgs = commentsContainer.querySelectorAll('.text-muted');
            noCommentsMsgs.forEach(function(msg) {
                if (msg.textContent.includes('No hay comentarios')) {
                    msg.remove();
                }
            });

            // Si no existe el contenedor, crearlo
            let commentsListDynamic = document.getElementById('comments-list');
            if (!commentsListDynamic) {
                commentsListDynamic = document.createElement('div');
                commentsListDynamic.id = 'comments-list';
                // Insertar antes del formulario si es posible
                if (commentForm && commentForm.parentNode) {
                    commentForm.parentNode.insertBefore(commentsListDynamic, commentForm);
                } else if (commentsContainer) {
                    commentsContainer.appendChild(commentsListDynamic);
                }
            }

            // Mostrar loader temporal
            let loader = document.createElement('div');
            loader.className = 'text-center text-secondary my-2 comment-loader';
            loader.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Agregando comentario...';
            commentsListDynamic.insertBefore(loader, commentsListDynamic.firstChild);

            // Deshabilitar el botón y mostrar loading
            submitButton.disabled = true;
            submitButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Enviando...';

            // Obtener los datos del formulario
            const formData = new FormData(commentForm);

            // Enviar la petición AJAX
            fetch(commentForm.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Eliminar loader
                    let loaderEl = commentsListDynamic.querySelector('.comment-loader');
                    if (loaderEl) loaderEl.remove();

                    // Limpiar el textarea y restaurar el botón
                    commentTextarea.value = '';
                    submitButton.disabled = false;
                    submitButton.innerHTML = 'Agregar Comentario';

                    // Refrescar la lista de comentarios (deja que updateComments y Pusher hagan el trabajo)
                    updateComments();

                    // Mostrar mensaje de éxito
                    showAlert(data.message, 'success', commentsContainer, 1500);
                } else {
                    throw new Error(data.message || 'Error al enviar el comentario');
                }
            })
            .catch(error => {
                // Eliminar loader si hay error
                let loaderEl = commentsListDynamic.querySelector('.comment-loader');
                if (loaderEl) loaderEl.remove();
                // Limpiar el textarea y restaurar el botón
                commentTextarea.value = '';
                submitButton.disabled = false;
                submitButton.innerHTML = 'Agregar Comentario';
                // Mostrar mensaje de error
                showAlert('Error al enviar el comentario. Por favor, intente nuevamente.', 'danger', commentsContainer, 2000);
            });
        });

        // Eliminar mensaje de 'No hay comentarios' al escribir en el textarea
        commentTextarea.addEventListener('input', function() {
            let noCommentsMsgs = commentsContainer.querySelectorAll('.text-muted');
            noCommentsMsgs.forEach(function(msg) {
                if (msg.textContent.includes('No hay comentarios')) {
                    msg.remove();
                }
            });
        });
    });

    document.addEventListener('DOMContentLoaded', function() {
        // Miniaturas de imagen: abrir modal con la imagen grande
        document.querySelectorAll('.doc-img-thumb').forEach(function(img) {
            img.addEventListener('click', function() {
                const modalImg = document.getElementById('modalImage');
                modalImg.src = this.getAttribute('data-img-src');
            });
        });
        // Limpiar imagen al cerrar el modal
        const imageModal = document.getElementById('imageModal');
        imageModal.addEventListener('hidden.bs.modal', function () {
            document.getElementById('modalImage').src = '';
        });
    });

    document.addEventListener('DOMContentLoaded', function() {
        let formToDelete = null;
        document.querySelectorAll('.btn-delete-document').forEach(btn => {
            btn.addEventListener('click', function() {
                formToDelete = this.closest('form');
                const modal = new bootstrap.Modal(document.getElementById('confirmDeleteDocumentModal'));
                modal.show();
            });
        });
        document.getElementById('confirmDeleteDocumentBtn').addEventListener('click', function() {
            if (formToDelete) {
                formToDelete.submit();
            }
        });
    });
</script>
@endpush

<style>
    .pill-btn-group {
        display: flex;
        flex-direction: row;
        border-radius: 18px;
        background: #f8fbff;
        width: 100%;
        justify-content: center;
        margin-top: 0.5rem;
        margin-bottom: 0.2rem;
        box-shadow: 0 2px 8px rgba(33,150,243,0.07);
        transition: box-shadow 0.2s;
        gap: 0.7rem;
        padding: 0.3rem 0.5rem;
    }
    .pill-btn {
        border: 2px solid #e3e8ee;
        background: #fff;
        width: 54px;
        height: 54px;
        font-size: 1.35em;
        font-weight: 500;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: background 0.18s, color 0.18s, box-shadow 0.18s, border-color 0.18s;
        outline: none;
        border-radius: 999px;
        margin: 0;
        box-shadow: none;
        min-width: 0;
        gap: 0;
        flex: 0 0 54px;
        cursor: pointer;
        padding: 0;
    }
    .pill-btn-download {
        color: #2196f3;
        border-color: #2196f3;
    }
    .pill-btn-download:hover, .pill-btn-download:focus {
        background: #2196f3;
        color: #fff;
        box-shadow: 0 2px 8px rgba(33,150,243,0.13);
        border-color: #2196f3;
    }
    .pill-btn-delete {
        color: #e53935;
        border-color: #e53935;
    }
    .pill-btn-delete:hover, .pill-btn-delete:focus {
        background: #e53935;
        color: #fff;
        box-shadow: 0 2px 8px rgba(229,57,53,0.13);
        border-color: #e53935;
    }
    .pill-btn i {
        font-size: 1.35em;
        margin: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 100%;
    }
    @media (max-width: 600px) {
        .pill-btn-group {
            flex-direction: row;
            border-radius: 18px;
            width: 100%;
            gap: 0.7rem;
            box-shadow: 0 2px 8px rgba(33,150,243,0.07);
            padding: 0.3rem 0.2rem;
        }
        .pill-btn {
            width: 48px;
            height: 48px;
            font-size: 1.15em;
            padding: 0;
            flex: 0 0 48px;
            justify-content: center;
            border-radius: 999px !important;
        }
    }
    .doc-card.card {
        border-radius: 18px;
        box-shadow: 0 2px 12px rgba(1,163,213,0.07), 0 1.5px 4px rgba(0,0,0,0.03);
        border: 1.5px solid #e3e8ee;
        padding: 0;
        margin-bottom: 1.2rem;
        min-height: 340px;
        background: #fff;
        transition: box-shadow 0.18s;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: flex-start;
    }
    .doc-card .card-body {
        width: 100%;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: flex-start;
        padding: 0 1.1rem 1.1rem 1.1rem;
    }
    .doc-img-thumb {
        object-fit: cover;
        width: 100%;
        max-width: 180px;
        max-height: 140px;
        border-radius: 16px;
        border: 1.5px solid #e3e8ee;
        box-shadow: 0 2px 8px rgba(33,150,243,0.07);
        margin-bottom: 0.5rem;
    }
    @media (max-width: 600px) {
        .doc-card.card {
            min-height: 220px;
            padding: 0;
        }
        .doc-card .card-body {
            padding: 0 0.5rem 0.7rem 0.5rem;
        }
        .doc-img-thumb {
            max-width: 100%;
            max-height: 110px;
        }
    }
</style>
@endsection