@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="card mb-4 shadow-sm">
            <div class="card-body">
                <div class="row align-items-center g-2">
                    <h4 class="mb-3">Bitácora de usuario: <span class="text-primary">{{ $user->name }}</span></h4>
                    <div class="row align-items-center g-2">
                        <form method="GET" action="{{ route('audit.profile', $user->id) }}" class="row">
                            <div class="col-md-4 mb-2">
                                <label for="action">Acción</label>
                                <select name="action" id="action" class="form-select">
                                    <option value="">Todas</option>
                                    @foreach ($accionesUnicas as $accion)
                                        <option value="{{ $accion }}" {{ request('action') == $accion ? 'selected' : '' }}>
                                            {{ $accion }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3 mb-2">
                                <label for="from">Desde</label>
                                <input type="date" name="from" id="from" class="form-control" value="{{ request('from') }}">
                            </div>
                            <div class="col-md-3 mb-2">
                                <label for="to">Hasta</label>
                                <input type="date" name="to" id="to" class="form-control" value="{{ request('to') }}">
                            </div>
                            <div class="col-md-2 d-flex align-items-end mb-2">
                                <button type="submit" class="btn btn-primary w-100">Filtrar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div id="audit-table">
                @include('audit.partials.logs', ['logs' => $logs])
            </div>
        </div>
@endsection