@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h2 class="mb-0">Nuevo Ticket</h2>
                </div>

                <div class="card-body">
                    <form method="POST" action="{{ route('tickets.store') }}">
                        @csrf

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="title" class="form-label">Título</label>
                                <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title') }}" required>
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="category_id" class="form-label">Categoría</label>
                                <select class="form-select @error('category_id') is-invalid @enderror" id="category_id" name="category_id" required>
                                    <option value="">Selecciona una categoría</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="priority" class="form-label">Prioridad</label>
                                <select class="form-select @error('priority') is-invalid @enderror" id="priority" name="priority" required>
                                    <option value="baja" {{ old('priority') == 'baja' ? 'selected' : '' }}>Baja</option>
                                    <option value="media" {{ old('priority') == 'media' ? 'selected' : '' }}>Media</option>
                                    <option value="alta" {{ old('priority') == 'alta' ? 'selected' : '' }}>Alta</option>
                                    <option value="urgente" {{ old('priority') == 'urgente' ? 'selected' : '' }}>Urgente</option>
                                </select>
                                @error('priority')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="status_id" class="form-label">Estado</label>
                                <input type="text" class="form-control" value="Solicitado" disabled>
                                <input type="hidden" name="status_id" value="{{ $statuses->where('name', 'Solicitado')->first()->id }}">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="due_date" class="form-label">Fecha límite</label>
                                <input type="datetime-local" class="form-control @error('due_date') is-invalid @enderror" id="due_date" name="due_date" value="{{ old('due_date') }}">
                                @error('due_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Descripción</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="4" required>{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <h4 class="mb-3">Información del Equipo</h4>
                        
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="marca" class="form-label">Marca</label>
                                <input type="text" class="form-control @error('marca') is-invalid @enderror" id="marca" name="marca" value="{{ old('marca') }}">
                                @error('marca')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="modelo" class="form-label">Modelo</label>
                                <input type="text" class="form-control @error('modelo') is-invalid @enderror" id="modelo" name="modelo" value="{{ old('modelo') }}">
                                @error('modelo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="numero_serie" class="form-label">Número de Serie</label>
                                <input type="text" class="form-control @error('numero_serie') is-invalid @enderror" id="numero_serie" name="numero_serie" value="{{ old('numero_serie') }}">
                                @error('numero_serie')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="ubicacion" class="form-label">Ubicación</label>
                                <input type="text" class="form-control @error('ubicacion') is-invalid @enderror" id="ubicacion" name="ubicacion" value="{{ old('ubicacion') }}">
                                @error('ubicacion')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="usuario" class="form-label">Usuario (Quien utiliza el equipo)</label>
                                <input type="text" class="form-control @error('usuario') is-invalid @enderror" id="usuario" name="usuario" value="{{ old('usuario') }}">
                                @error('usuario')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="ip_red_wifi" class="form-label">IP/Red WiFi</label>
                                <input type="text" class="form-control @error('ip_red_wifi') is-invalid @enderror" id="ip_red_wifi" name="ip_red_wifi" value="{{ old('ip_red_wifi') }}">
                                @error('ip_red_wifi')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <h4 class="mb-3">Especificaciones Técnicas</h4>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="cpu" class="form-label">CPU</label>
                                <input type="text" class="form-control @error('cpu') is-invalid @enderror" id="cpu" name="cpu" value="{{ old('cpu') }}">
                                @error('cpu')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="ram" class="form-label">RAM</label>
                                <input type="text" class="form-control @error('ram') is-invalid @enderror" id="ram" name="ram" value="{{ old('ram') }}">
                                @error('ram')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="capacidad_almacenamiento" class="form-label">Capacidad de Almacenamiento</label>
                                <input type="text" class="form-control @error('capacidad_almacenamiento') is-invalid @enderror" id="capacidad_almacenamiento" name="capacidad_almacenamiento" value="{{ old('capacidad_almacenamiento') }}">
                                @error('capacidad_almacenamiento')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="tarjeta_video" class="form-label">Tarjeta de Video</label>
                                <input type="text" class="form-control @error('tarjeta_video') is-invalid @enderror" id="tarjeta_video" name="tarjeta_video" value="{{ old('tarjeta_video') }}">
                                @error('tarjeta_video')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <h4 class="mb-3">Información de Acceso Remoto</h4>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="id_anydesk" class="form-label">ID AnyDesk</label>
                                <input type="text" class="form-control @error('id_anydesk') is-invalid @enderror" id="id_anydesk" name="id_anydesk" value="{{ old('id_anydesk') }}">
                                @error('id_anydesk')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="pass_anydesk" class="form-label">Contraseña AnyDesk</label>
                                <input type="text" class="form-control @error('pass_anydesk') is-invalid @enderror" id="pass_anydesk" name="pass_anydesk" value="{{ old('pass_anydesk') }}">
                                @error('pass_anydesk')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="version_windows" class="form-label">Versión Windows</label>
                                <input type="text" class="form-control @error('version_windows') is-invalid @enderror" id="version_windows" name="version_windows" value="{{ old('version_windows') }}">
                                @error('version_windows')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="licencia_windows" class="form-label">Licencia Windows</label>
                                <input type="text" class="form-control @error('licencia_windows') is-invalid @enderror" id="licencia_windows" name="licencia_windows" value="{{ old('licencia_windows') }}">
                                @error('licencia_windows')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="version_office" class="form-label">Versión Office</label>
                                <input type="text" class="form-control @error('version_office') is-invalid @enderror" id="version_office" name="version_office" value="{{ old('version_office') }}">
                                @error('version_office')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="licencia_office" class="form-label">Licencia Office</label>
                                <input type="text" class="form-control @error('licencia_office') is-invalid @enderror" id="licencia_office" name="licencia_office" value="{{ old('licencia_office') }}">
                                @error('licencia_office')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="password_cuenta" class="form-label">Contraseña de Cuenta</label>
                                <input type="text" class="form-control @error('password_cuenta') is-invalid @enderror" id="password_cuenta" name="password_cuenta" value="{{ old('password_cuenta') }}">
                                @error('password_cuenta')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="fecha_instalacion" class="form-label">Fecha de Instalación</label>
                                <input type="date" class="form-control @error('fecha_instalacion') is-invalid @enderror" id="fecha_instalacion" name="fecha_instalacion" value="{{ old('fecha_instalacion') }}">
                                @error('fecha_instalacion')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="comentarios" class="form-label">Comentarios sobre el ticket (Tipo de error/falla/problema/etc)</label>
                                <textarea class="form-control @error('comentarios') is-invalid @enderror" id="comentarios" name="comentarios" rows="3">{{ old('comentarios') }}</textarea>
                                @error('comentarios')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('tickets.index') }}" class="btn btn-secondary">Cancelar</a>
                            <button type="submit" class="btn btn-primary">Crear Ticket</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 