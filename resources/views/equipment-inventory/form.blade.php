@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h2 class="mb-0">{{ isset($equipmentInventory) ? 'Editar Equipo' : 'Nuevo Equipo' }}</h2>
                </div>

                <div class="card-body">
                    <form action="{{ isset($equipmentInventory) ? route('equipment-inventory.update', $equipmentInventory) : route('equipment-inventory.store') }}" method="POST">
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        @csrf
                        @if(isset($equipmentInventory))
                            @method('PUT')
                        @endif

                        <!-- Información General del Equipo -->
                        <h4 class="mb-3">Información General del Equipo</h4>
                        <div class="mb-3">
                            <div class="col-md-12">
                                <label for="marca" class="form-label">Marca</label>
                                <input type="text" class="form-control @error('marca') is-invalid @enderror" 
                                       id="marca" name="marca" 
                                       value="{{ old('marca', $equipmentInventory->marca ?? '') }}" required>
                                @error('marca')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="col-md-12">
                                <label for="modelo" class="form-label">Modelo</label>
                                <input type="text" class="form-control @error('modelo') is-invalid @enderror" 
                                       id="modelo" name="modelo" 
                                       value="{{ old('modelo', $equipmentInventory->modelo ?? '') }}" required>
                                @error('modelo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="col-md-12">
                                <label for="numero_serie" class="form-label">Número de Serie</label>
                                <input type="text" class="form-control @error('numero_serie') is-invalid @enderror" 
                                       id="numero_serie" name="numero_serie" 
                                       value="{{ old('numero_serie', $equipmentInventory->numero_serie ?? '') }}" required>
                                <div id="serialNumberExistsMessage" class="text-danger mt-1" style="display: none;">
                                    Este número de serie ya existe en el inventario.
                                </div>
                                @error('numero_serie')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="col-md-12">
                                <label for="tipo" class="form-label">Tipo</label>
                                <select class="form-control @error('tipo') is-invalid @enderror" id="tipo" name="tipo" required>
                                    <option value="">Selecciona un tipo</option>
                                    <option value="Notebook" {{ old('tipo', $equipmentInventory->tipo ?? '') == 'Notebook' ? 'selected' : '' }}>Notebook</option>
                                    <option value="Escritorio" {{ old('tipo', $equipmentInventory->tipo ?? '') == 'Escritorio' ? 'selected' : '' }}>Escritorio</option>
                                    <option value="Servidor" {{ old('tipo', $equipmentInventory->tipo ?? '') == 'Servidor' ? 'selected' : '' }}>Servidor</option>
                                    <option value="Impresora" {{ old('tipo', $equipmentInventory->tipo ?? '') == 'Impresora' ? 'selected' : '' }}>Impresora</option>
                                    <option value="Otro" {{ old('tipo', $equipmentInventory->tipo ?? '') == 'Otro' ? 'selected' : '' }}>Otro</option>
                                </select>
                                @error('tipo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="col-md-12">
                                <label for="estado" class="form-label">Estado</label>
                                <select class="form-control @error('estado') is-invalid @enderror" id="estado" name="estado" required>
                                    <option value="">Selecciona un estado</option>
                                    <option value="Activo" {{ old('estado', $equipmentInventory->estado ?? '') == 'Activo' ? 'selected' : '' }}>Activo</option>
                                    <option value="En Reparación" {{ old('estado', $equipmentInventory->estado ?? '') == 'En Reparación' ? 'selected' : '' }}>En Reparación</option>
                                    <option value="Inactivo" {{ old('estado', $equipmentInventory->estado ?? '') == 'Inactivo' ? 'selected' : '' }}>Inactivo</option>
                                    <option value="Dado de Baja" {{ old('estado', $equipmentInventory->estado ?? '') == 'Dado de Baja' ? 'selected' : '' }}>Dado de Baja</option>
                                </select>
                                @error('estado')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Ubicación y Usuario -->
                        <h4 class="mt-4 mb-3">Ubicación y Usuario</h4>
                        <div class="mb-3">
                            <div class="col-md-12">
                                <label for="location_id" class="form-label">Centro</label>
                                <select class="form-control @error('location_id') is-invalid @enderror" id="location_id" name="location_id" required>
                                    <option value="">Selecciona una ubicación</option>
                                    @foreach($locations as $location)
                                        <option value="{{ $location->id }}" {{ old('location_id', $equipmentInventory->location_id ?? '') == $location->id ? 'selected' : '' }}>{{ $location->name }}</option>
                                    @endforeach
                                </select>
                                @error('location_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="col-md-12">
                                <label for="usuario" class="form-label">Usuario</label>
                                <input type="text" class="form-control @error('usuario') is-invalid @enderror" 
                                       id="usuario" name="usuario" 
                                       value="{{ old('usuario', $equipmentInventory->usuario ?? '') }}" required>
                                @error('usuario')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-12">
                                <label for="box_oficina" class="form-label">Box/Oficina</label>
                                <input type="text" class="form-control @error('box_oficina') is-invalid @enderror" 
                                       id="box_oficina" name="box_oficina" 
                                       value="{{ old('box_oficina', $equipmentInventory->box_oficina ?? '') }}" required>
                                @error('box_oficina')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Especificaciones Técnicas y Software -->
                        <h4 class="mt-4 mb-3">Especificaciones Técnicas y Software</h4>
                        <div class="mb-3">
                            <div class="col-md-12">
                                <label for="ip_red_wifi" class="form-label">IP/Red WiFi</label>
                                <input type="text" class="form-control @error('ip_red_wifi') is-invalid @enderror" 
                                       id="ip_red_wifi" name="ip_red_wifi" 
                                       value="{{ old('ip_red_wifi', $equipmentInventory->ip_red_wifi ?? '') }}">
                                @error('ip_red_wifi')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="col-md-12">
                                <label for="cpu" class="form-label">CPU</label>
                                <input type="text" class="form-control @error('cpu') is-invalid @enderror" 
                                       id="cpu" name="cpu" 
                                       value="{{ old('cpu', $equipmentInventory->cpu ?? '') }}">
                                @error('cpu')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="col-md-12">
                                <label for="ram" class="form-label">RAM</label>
                                <input type="text" class="form-control @error('ram') is-invalid @enderror" 
                                       id="ram" name="ram" 
                                       value="{{ old('ram', $equipmentInventory->ram ?? '') }}">
                                @error('ram')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="col-md-12">
                                <label for="capacidad_almacenamiento" class="form-label">Capacidad de Almacenamiento</label>
                                <input type="text" class="form-control @error('capacidad_almacenamiento') is-invalid @enderror" 
                                       id="capacidad_almacenamiento" name="capacidad_almacenamiento" 
                                       value="{{ old('capacidad_almacenamiento', $equipmentInventory->capacidad_almacenamiento ?? '') }}">
                                @error('capacidad_almacenamiento')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="col-md-12">
                                <label for="tarjeta_video" class="form-label">Tarjeta de Video</label>
                                <input type="text" class="form-control @error('tarjeta_video') is-invalid @enderror" 
                                       id="tarjeta_video" name="tarjeta_video" 
                                       value="{{ old('tarjeta_video', $equipmentInventory->tarjeta_video ?? '') }}">
                                @error('tarjeta_video')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="col-md-12">
                                <label for="version_windows" class="form-label">Versión Windows</label>
                                <input type="text" class="form-control @error('version_windows') is-invalid @enderror" 
                                       id="version_windows" name="version_windows" 
                                       value="{{ old('version_windows', $equipmentInventory->version_windows ?? '') }}">
                                @error('version_windows')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="col-md-12">
                                <label for="licencia_windows" class="form-label">Licencia Windows</label>
                                <input type="text" class="form-control @error('licencia_windows') is-invalid @enderror" 
                                       id="licencia_windows" name="licencia_windows" 
                                       value="{{ old('licencia_windows', $equipmentInventory->licencia_windows ?? '') }}">
                                @error('licencia_windows')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="col-md-12">
                                <label for="version_office" class="form-label">Versión Office</label>
                                <input type="text" class="form-control @error('version_office') is-invalid @enderror" 
                                       id="version_office" name="version_office" 
                                       value="{{ old('version_office', $equipmentInventory->version_office ?? '') }}">
                                @error('version_office')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="col-md-12">
                                <label for="licencia_office" class="form-label">Licencia Office</label>
                                <input type="text" class="form-control @error('licencia_office') is-invalid @enderror" 
                                       id="licencia_office" name="licencia_office" 
                                       value="{{ old('licencia_office', $equipmentInventory->licencia_office ?? '') }}">
                                @error('licencia_office')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="col-md-12">
                                <label for="fecha_instalacion" class="form-label">Fecha de Instalación</label>
                                <input type="date" class="form-control @error('fecha_instalacion') is-invalid @enderror" 
                                       id="fecha_instalacion" name="fecha_instalacion" 
                                       value="{{ old('fecha_instalacion', isset($equipmentInventory) ? $equipmentInventory->fecha_instalacion->format('Y-m-d') : '') }}">
                                @error('fecha_instalacion')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Información de Acceso Remoto y Comentarios -->
                        <h4 class="mt-4 mb-3">Información de Acceso Remoto y Comentarios</h4>
                        <div class="mb-3">
                            <div class="col-md-12">
                                <label for="id_anydesk" class="form-label">ID AnyDesk</label>
                                <input type="text" class="form-control @error('id_anydesk') is-invalid @enderror" 
                                       id="id_anydesk" name="id_anydesk" 
                                       value="{{ old('id_anydesk', $equipmentInventory->id_anydesk ?? '') }}">
                                @error('id_anydesk')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="col-md-12">
                                <label for="pass_anydesk" class="form-label">Password AnyDesk</label>
                                <input type="text" class="form-control @error('pass_anydesk') is-invalid @enderror" 
                                       id="pass_anydesk" name="pass_anydesk" 
                                       value="{{ old('pass_anydesk', $equipmentInventory->pass_anydesk ?? '') }}">
                                @error('pass_anydesk')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="col-md-12">
                                <label for="password_cuenta" class="form-label">Password Cuenta</label>
                                <input type="text" class="form-control @error('password_cuenta') is-invalid @enderror" 
                                       id="password_cuenta" name="password_cuenta" 
                                       value="{{ old('password_cuenta', $equipmentInventory->password_cuenta ?? '') }}">
                                @error('password_cuenta')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="col-md-12">
                                <label for="comentarios" class="form-label">Comentarios</label>
                                <textarea class="form-control @error('comentarios') is-invalid @enderror" 
                                          id="comentarios" name="comentarios" rows="3">{{ old('comentarios', $equipmentInventory->comentarios ?? '') }}</textarea>
                                @error('comentarios')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('equipment-inventory.index') }}" class="btn btn-secondary">Cancelar</a>
                            <button type="submit" class="btn btn-primary">
                                {{ isset($equipmentInventory) ? 'Actualizar' : 'Guardar' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const serialNumberInput = document.getElementById('numero_serie');
        const serialNumberMessage = document.getElementById('serialNumberExistsMessage');
        let typingTimer;                // timer identifier
        const doneTypingInterval = 500; // time in ms (0.5 seconds)

        // On keyup, start the countdown
        serialNumberInput.addEventListener('input', () => {
            clearTimeout(typingTimer);
            if (serialNumberInput.value) {
                typingTimer = setTimeout(checkSerialNumber, doneTypingInterval);
            }
        });

        function checkSerialNumber() {
            const serialNumber = serialNumberInput.value;
            // Solo verificar si no estamos en modo de edición y el número de serie es el mismo que el original
            // O si es un nuevo equipo
            const currentSerialNumber = "{{ $equipmentInventory->numero_serie ?? null }}";

            if (serialNumber && serialNumber !== currentSerialNumber) {
                fetch('/equipment-inventory/check-serial-number', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ numero_serie: serialNumber })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.exists) {
                        serialNumberMessage.style.display = 'block';
                        serialNumberInput.classList.add('is-invalid');
                    } else {
                        serialNumberMessage.style.display = 'none';
                        serialNumberInput.classList.remove('is-invalid');
                    }
                })
                .catch(error => console.error('Error:', error));
            } else {
                 // Si es el número de serie actual o está vacío, ocultar el mensaje y eliminar la validación visual
                serialNumberMessage.style.display = 'none';
                serialNumberInput.classList.remove('is-invalid');
            }
        }

        // Ejecutar la verificación inicial si el campo ya tiene un valor (en caso de edición)
        if (serialNumberInput.value) {
            // No es necesario verificar al cargar si es el mismo serial number del equipo que se está editando.
            // La verificación solo se necesita cuando el usuario cambia el valor.
            // Si se carga el formulario con un valor existente, no debemos mostrar la advertencia si es su propio serial.
        }
    });
</script>
@endsection 