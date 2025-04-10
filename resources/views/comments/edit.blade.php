@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Editar Comentario</h2>

    <form method="POST" action="{{ route('comments.update', $comment) }}">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="comment" class="form-label">Comentario</label>
            <textarea name="comment" id="comment" class="form-control" rows="3" required>{{ old('comment', $comment->comment) }}</textarea>
        </div>

        <div class="mb-3 form-check">
            <input type="checkbox" class="form-check-input" id="is_internal" name="is_internal" value="1"
                {{ $comment->is_internal ? 'checked' : '' }}>
            <label class="form-check-label" for="is_internal">Comentario interno</label>
        </div>

        <button type="submit" class="btn btn-primary">Actualizar</button>
        <a href="{{ route('tickets.show', $comment->ticket_id) }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
@endsection
