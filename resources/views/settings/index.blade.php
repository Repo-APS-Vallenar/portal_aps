@extends('layouts.app')

@section('content')
<div class="container mt-5 text-center">
    <h2>⚙️ Configuración General</h2>

    <form method="POST" action="{{ route('settings.toggle-maintenance') }}">
        @csrf
        <button class="btn btn-warning mt-3">
            @if($maintenance === 'on')
                🔧 Desactivar Mantenimiento
            @else
                🔧 Activar Mantenimiento
            @endif
        </button>
    </form>
</div>
@endsection
