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

    <!-- Botón para eliminar (solo si es admin o el autor del comentario) -->
    @if(Auth::user()->isAdmin() || Auth::id() === $comment->user_id)
    <div class="mt-2">
        <button type="button" 
                class="btn btn-sm btn-outline-danger"
                data-bs-toggle="modal" 
                data-bs-target="#confirmDeleteCommentModal"
                data-comment-id="{{ $comment->id }}"
                data-ticket-id="{{ $ticket->id }}">
            Eliminar
        </button>
    </div>
    @endif
</div>
@endif 