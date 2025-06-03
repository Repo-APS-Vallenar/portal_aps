@extends('layouts.app')
@section('title', 'No encontrado')
@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 text-center">
            <div class="card shadow-sm p-4" style="border-radius: 18px;">
                <h1 class="display-4 mb-3" style="color: #01a3d5;">404</h1>
                @if(str_contains(request()->path(), 'tickets'))
                    <h2 class="mb-3">El ticket que buscas no existe o ha sido eliminado.</h2>
                    <p class="mb-4">Es posible que el ticket haya sido eliminado o que la URL sea incorrecta.</p>
                    <a href="{{ route('tickets.index') }}" class="btn btn-primary">
                        <i class="fas fa-arrow-left me-1"></i> Volver al listado de tickets
                    </a>
                @else
                    <h2 class="mb-3">Página no encontrada</h2>
                    <p class="mb-4">La página que buscas no existe o ha sido movida.</p>
                    <a href="{{ url('/') }}" class="btn btn-primary">
                        <i class="fas fa-home me-1"></i> Ir al inicio
                    </a>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection 