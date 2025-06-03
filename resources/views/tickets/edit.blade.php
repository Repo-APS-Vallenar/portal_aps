@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h2 class="mb-0">Editar Ticket Enviado por: {{ $ticket->title }}</h2>
                </div>

                <div class="card-body">
                    <form action="{{ route('tickets.update', $ticket) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="title" class="form-label">Nombre del funcionario</label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror"
                                id="title" name="title" value="{{ old('title', $ticket->title) }}" required>
                            @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Descripción</label>
                            <textarea class="form-control @error('description') is-invalid @enderror"
                                id="description" name="description" rows="4" required>{{ old('description', $ticket->description) }}</textarea>
                            @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="category_id" class="form-label">Categoría</label>
                            <select class="form-select @error('category_id') is-invalid @enderror"
                                id="category_id" name="category_id" required>
                                <option value="">Selecciona una categoría</option>
                                @php
                                $categoryIds = [];
                                @endphp
                                @foreach($categories as $category)
                                @if(!in_array($category->id, $categoryIds))
                                @php
                                $categoryIds[] = $category->id;
                                @endphp
                                <option value="{{ $category->id }}"
                                    {{ old('category_id', $ticket->category_id) == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                                @endif
                                @endforeach
                            </select>
                            @error('category_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="status_id" class="form-label">Estado</label>
                            @if(Auth::user()->isAdmin() || Auth::user()->isSuperadmin())
                            <select class="form-select @error('status_id') is-invalid @enderror"
                                id="status_id" name="status_id" required>
                                <option value="">Selecciona un estado</option>
                                @php
                                $statusIds = [];
                                @endphp
                                @foreach($statuses as $status)
                                @if(!in_array($status->id, $statusIds))
                                @php
                                $statusIds[] = $status->id;
                                @endphp
                                <option value="{{ $status->id }}"
                                    {{ old('status_id', $ticket->status_id) == $status->id ? 'selected' : '' }}>
                                    {{ $status->name }}
                                </option>
                                @endif
                                @endforeach
                            </select>
                            @else
                            <input type="text" class="form-control" value="{{ $ticket->status->name }}" disabled>
                            <input type="hidden" name="status_id" value="{{ $ticket->status_id }}">
                            @endif
                            @error('status_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="priority" class="form-label">Prioridad</label>
                            <select class="form-select @error('priority') is-invalid @enderror"
                                id="priority" name="priority" required>
                                @foreach(App\Models\Ticket::$priorities as $priority)
                                <option value="{{ $priority }}"
                                    {{ old('priority', $ticket->priority) == $priority ? 'selected' : '' }}>
                                    {{ ucfirst($priority) }}
                                </option>
                                @endforeach
                            </select>
                            @error('priority')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="contact_email" class="form-label">Correo de contacto</label>
                            <input type="email" name="contact_email" class="form-control" value="{{ old('contact_email', $ticket->contact_email ?? '') }}">
                        </div>

                        <div class="mb-3">
                            <label for="contact_phone" class="form-label">Teléfono de contacto</label>
                            <input type="text" name="contact_phone" class="form-control" value="{{ old('contact_phone', $ticket->contact_phone ?? '') }}">
                        </div>
                        <div class="mb-3">
                            <label for="assigned_to" class="form-label">Asignar a</label>
                            <select class="form-select @error('assigned_to') is-invalid @enderror"
                                id="assigned_to" name="assigned_to">
                                <option value="">Sin asignar</option>
                                @foreach($users as $user)
                                <option value="{{ $user->id }}"
                                    {{ old('assigned_to', $ticket->assigned_to) == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }}
                                </option>
                                @endforeach
                            </select>
                            @error('assigned_to')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <hr>

                        <h4>Información del Equipo</h4>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="marca" class="form-label">Marca</label>
                                <input type="text" class="form-control @error('marca') is-invalid @enderror"
                                    id="marca" name="marca" value="{{ old('marca', $ticket->marca) }}">
                                @error('marca')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="modelo" class="form-label">Modelo</label>
                                <input type="text" class="form-control @error('modelo') is-invalid @enderror"
                                    id="modelo" name="modelo" value="{{ old('modelo', $ticket->modelo) }}">
                                @error('modelo')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="numero_serie" class="form-label">Número de Serie</label>
                                <input type="text" class="form-control @error('numero_serie') is-invalid @enderror"
                                    id="numero_serie" name="numero_serie" value="{{ old('numero_serie', $ticket->numero_serie) }}">
                                @error('numero_serie')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="location_id" class="form-label">Ubicación</label>
                                <select class="form-select" id="location_id" name="location_id">
                                    <option value="">-- Selecciona una ubicación --</option>
                                    @foreach($locations as $location)
                                    <option value="{{ $location->id }}" {{ $ticket->location_id == $location->id ? 'selected' : '' }}>
                                        {{ $location->name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="usuario" class="form-label">Usuario</label>
                                <input type="text" class="form-control @error('usuario') is-invalid @enderror"
                                    id="usuario" name="usuario" value="{{ old('usuario', $ticket->usuario) }}">
                                @error('usuario')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="ip_red_wifi" class="form-label">IP Red WiFi</label>
                                <input type="text" class="form-control @error('ip_red_wifi') is-invalid @enderror"
                                    id="ip_red_wifi" name="ip_red_wifi" value="{{ old('ip_red_wifi', $ticket->ip_red_wifi) }}">
                                @error('ip_red_wifi')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>


                            <div class="col-md-6 mb-3">
                                <label for="cpu" class="form-label">CPU</label>
                                <input type="text" class="form-control @error('cpu') is-invalid @enderror"
                                    id="cpu" name="cpu" value="{{ old('cpu', $ticket->cpu) }}">
                                @error('cpu')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="ram" class="form-label">RAM</label>
                                <input type="text" class="form-control @error('ram') is-invalid @enderror"
                                    id="ram" name="ram" value="{{ old('ram', $ticket->ram) }}">
                                @error('ram')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>


                            <div class="col-md-6 mb-3">
                                <label for="capacidad_almacenamiento" class="form-label">Capacidad de Almacenamiento</label>
                                <input type="text" class="form-control @error('capacidad_almacenamiento') is-invalid @enderror"
                                    id="capacidad_almacenamiento" name="capacidad_almacenamiento"
                                    value="{{ old('capacidad_almacenamiento', $ticket->capacidad_almacenamiento) }}">
                                @error('capacidad_almacenamiento')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="tarjeta_video" class="form-label">Tarjeta de Video</label>
                                <input type="text" class="form-control @error('tarjeta_video') is-invalid @enderror"
                                    id="tarjeta_video" name="tarjeta_video" value="{{ old('tarjeta_video', $ticket->tarjeta_video) }}">
                                @error('tarjeta_video')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>



                            <div class="col-md-6 mb-3">
                                <label for="id_anydesk" class="form-label">ID AnyDesk</label>
                                <input type="text" class="form-control @error('id_anydesk') is-invalid @enderror"
                                    id="id_anydesk" name="id_anydesk" value="{{ old('id_anydesk', $ticket->id_anydesk) }}">
                                @error('id_anydesk')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="pass_anydesk" class="form-label">Contraseña AnyDesk</label>
                                <input type="text" class="form-control @error('pass_anydesk') is-invalid @enderror"
                                    id="pass_anydesk" name="pass_anydesk" value="{{ old('pass_anydesk', $ticket->pass_anydesk) }}">
                                @error('pass_anydesk')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3" id="solucion-aplicada-group" style="display: none;">
                                <label for="solucion_aplicada" class="form-label">Solución Aplicada <span class="text-danger">*</span></label>
                                <textarea class="form-control @error('solucion_aplicada') is-invalid @enderror" id="solucion_aplicada" name="solucion_aplicada" rows="3">{{ old('solucion_aplicada', $ticket->solucion_aplicada) }}</textarea>
                                @error('solucion_aplicada')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="d-flex justify-content-between">
                                <a href="{{ route('tickets.show', $ticket) }}" class="btn btn-secondary">Cancelar</a>
                                <button type="submit" class="btn btn-gradient">Actualizar Ticket</button>
                            </div>
                    </form>

                    @if(Auth::user() && (Auth::user()->role === 'admin' || Auth::user()->role === 'superadmin'))
                        @include('tickets.partials.document-upload', ['ticket' => $ticket])
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function toggleSolucionAplicada() {
        const statusSelect = document.getElementById('status_id');
        const solucionGroup = document.getElementById('solucion-aplicada-group');
        const solucionField = document.getElementById('solucion_aplicada');
        let isResuelto = false;
        if (statusSelect) {
            const selectedOption = statusSelect.options[statusSelect.selectedIndex];
            isResuelto = selectedOption && selectedOption.text.trim().toLowerCase() === 'resuelto';
        }
        if (isResuelto) {
            solucionGroup.style.display = '';
            solucionField.setAttribute('required', 'required');
        } else {
            solucionGroup.style.display = 'none';
            solucionField.removeAttribute('required');
        }
    }
    document.addEventListener('DOMContentLoaded', function() {
        const statusSelect = document.getElementById('status_id');
        if (statusSelect) {
            statusSelect.addEventListener('change', toggleSolucionAplicada);
            toggleSolucionAplicada();
        }
    });
</script>
@endpush