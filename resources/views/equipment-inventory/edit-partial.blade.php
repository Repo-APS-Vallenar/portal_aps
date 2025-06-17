<form action="{{ route('equipment-inventory.update', $equipment) }}" method="POST">
    @csrf
    @method('PUT')
    <div class="container-fluid">
        <div class="row">
            <!-- Información General del Equipo -->
            <h5 class="mb-3">Información General del Equipo</h5>
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="marca" class="form-label">Marca</label>
                    <input type="text" class="form-control @error('marca') is-invalid @enderror" 
                           id="marca" name="marca" 
                           value="{{ old('marca', $equipment->marca ?? '') }}" required>
                    @error('marca')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="col-md-6">
                <div class="mb-3">
                    <label for="modelo" class="form-label">Modelo</label>
                    <input type="text" class="form-control @error('modelo') is-invalid @enderror" 
                           id="modelo" name="modelo" 
                           value="{{ old('modelo', $equipment->modelo ?? '') }}" required>
                    @error('modelo')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="col-md-6">
                <div class="mb-3">
                    <label for="numero_serie" class="form-label">Número de Serie</label>
                    <input type="text" class="form-control @error('numero_serie') is-invalid @enderror" 
                           id="numero_serie" name="numero_serie" 
                           value="{{ old('numero_serie', $equipment->numero_serie ?? '') }}" required>
                    @error('numero_serie')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="col-md-6">
                <div class="mb-3">
                    <label for="tipo" class="form-label">Tipo</label>
                    <select class="form-control @error('tipo') is-invalid @enderror" id="tipo" name="tipo" required>
                        <option value="">Selecciona un tipo</option>
                        <option value="Notebook" {{ old('tipo', $equipment->tipo ?? '') == 'Notebook' ? 'selected' : '' }}>Notebook</option>
                        <option value="AIO" {{ old('tipo', $equipment->tipo ?? '') == 'AIO' ? 'selected' : '' }}>AIO (All in One)</option>
                        <option value="Servidor" {{ old('tipo', $equipment->tipo ?? '') == 'Servidor' ? 'selected' : '' }}>Servidor</option>
                        <option value="Impresora" {{ old('tipo', $equipment->tipo ?? '') == 'Impresora' ? 'selected' : '' }}>Impresora</option>
                        <option value="SwitchRouter" {{ old('tipo', $equipment->tipo ?? '') == 'SwitchRouter' ? 'selected' : '' }}>Switch/Router</option>
                        <option value="Otro" {{ old('tipo', $equipment->tipo ?? '') == 'Otro' ? 'selected' : '' }}>Otro</option>
                    </select>
                    @error('tipo')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="col-md-6">
                <div class="mb-3">
                    <label for="estado" class="form-label">Estado</label>
                    <select class="form-control @error('estado') is-invalid @enderror" id="estado" name="estado" required>
                        <option value="">Selecciona un estado</option>
                        <option value="Activo" {{ old('estado', $equipment->estado ?? '') == 'Activo' ? 'selected' : '' }}>Activo</option>
                        <option value="En Reparación" {{ old('estado', $equipment->estado ?? '') == 'En Reparación' ? 'selected' : '' }}>En Reparación</option>
                        <option value="Inactivo" {{ old('estado', $equipment->estado ?? '') == 'Inactivo' ? 'selected' : '' }}>Inactivo</option>
                        <option value="Dado de Baja" {{ old('estado', $equipment->estado ?? '') == 'Dado de Baja' ? 'selected' : '' }}>Dado de Baja</option>
                    </select>
                    @error('estado')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="col-md-6">
                <div class="mb-3">
                    <label for="usuario" class="form-label">Usuario</label>
                    <input type="text" class="form-control @error('usuario') is-invalid @enderror" 
                           id="usuario" name="usuario" 
                           value="{{ old('usuario', $equipment->usuario ?? '') }}">
                    @error('usuario')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="col-md-6">
                <div class="mb-3">
                    <label for="box_oficina" class="form-label">Box/Oficina</label>
                    <input type="text" class="form-control @error('box_oficina') is-invalid @enderror" 
                           id="box_oficina" name="box_oficina" 
                           value="{{ old('box_oficina', $equipment->box_oficina ?? '') }}">
                </div>
            </div>

            <div class="col-md-6">
                <div class="mb-3">
                    <label for="location_id" class="form-label">Ubicación</label>
                    <select class="form-control @error('location_id') is-invalid @enderror" id="location_id" name="location_id" required>
                        <option value="">Selecciona una ubicación</option>
                        @foreach($locations as $location)
                            <option value="{{ $location->id }}" {{ old('location_id', $equipment->location_id ?? '') == $location->id ? 'selected' : '' }}>{{ $location->name }}</option>
                        @endforeach
                    </select>
                    @error('location_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Fechas -->
            <h5 class="mt-4 mb-3">Fechas</h5>
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="fecha_adquisicion" class="form-label">Fecha Adquisición</label>
                    <input type="date" class="form-control @error('fecha_adquisicion') is-invalid @enderror" 
                           id="fecha_adquisicion" name="fecha_adquisicion" 
                           value="{{ old('fecha_adquisicion', $equipment->fecha_adquisicion ? $equipment->fecha_adquisicion->format('Y-m-d') : '') }}">
                    @error('fecha_adquisicion')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="ultima_mantenimiento" class="form-label">Último Mantenimiento</label>
                    <input type="date" class="form-control @error('ultima_mantenimiento') is-invalid @enderror" 
                           id="ultima_mantenimiento" name="ultima_mantenimiento" 
                           value="{{ old('ultima_mantenimiento', $equipment->ultima_mantenimiento ? $equipment->ultima_mantenimiento->format('Y-m-d') : '') }}">
                    @error('ultima_mantenimiento')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="proximo_mantenimiento" class="form-label">Próximo Mantenimiento</label>
                    <input type="date" class="form-control @error('proximo_mantenimiento') is-invalid @enderror" 
                           id="proximo_mantenimiento" name="proximo_mantenimiento" 
                           value="{{ old('proximo_mantenimiento', $equipment->proximo_mantenimiento ? $equipment->proximo_mantenimiento->format('Y-m-d') : '') }}">
                    @error('proximo_mantenimiento')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="fecha_instalacion" class="form-label">Fecha Instalación</label>
                    <input type="date" class="form-control @error('fecha_instalacion') is-invalid @enderror" 
                           id="fecha_instalacion" name="fecha_instalacion" 
                           value="{{ old('fecha_instalacion', $equipment->fecha_instalacion ? $equipment->fecha_instalacion->format('Y-m-d') : '') }}">
                    @error('fecha_instalacion')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Especificaciones Técnicas Detalladas -->
            <h5 class="mt-4 mb-3">Especificaciones Técnicas Detalladas</h5>
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="ip_red_wifi" class="form-label">IP/Red WiFi</label>
                    <input type="text" class="form-control @error('ip_red_wifi') is-invalid @enderror" 
                           id="ip_red_wifi" name="ip_red_wifi" 
                           value="{{ old('ip_red_wifi', $equipment->ip_red_wifi ?? '') }}">
                    @error('ip_red_wifi')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="cpu" class="form-label">CPU</label>
                    <input type="text" class="form-control @error('cpu') is-invalid @enderror" 
                           id="cpu" name="cpu" 
                           value="{{ old('cpu', $equipment->cpu ?? '') }}">
                    @error('cpu')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="ram" class="form-label">RAM</label>
                    <input type="text" class="form-control @error('ram') is-invalid @enderror" 
                           id="ram" name="ram" 
                           value="{{ old('ram', $equipment->ram ?? '') }}">
                    @error('ram')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="capacidad_almacenamiento" class="form-label">Capacidad Almacenamiento</label>
                    <input type="text" class="form-control @error('capacidad_almacenamiento') is-invalid @enderror" 
                           id="capacidad_almacenamiento" name="capacidad_almacenamiento" 
                           value="{{ old('capacidad_almacenamiento', $equipment->capacidad_almacenamiento ?? '') }}">
                    @error('capacidad_almacenamiento')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="tarjeta_video" class="form-label">Tarjeta de Video</label>
                    <input type="text" class="form-control @error('tarjeta_video') is-invalid @enderror" 
                           id="tarjeta_video" name="tarjeta_video" 
                           value="{{ old('tarjeta_video', $equipment->tarjeta_video ?? '') }}">
                    @error('tarjeta_video')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Información de Software -->
            <h5 class="mt-4 mb-3">Información de Software</h5>
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="version_windows" class="form-label">Versión Windows</label>
                    <input type="text" class="form-control @error('version_windows') is-invalid @enderror" 
                           id="version_windows" name="version_windows" 
                           value="{{ old('version_windows', $equipment->version_windows ?? '') }}">
                    @error('version_windows')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="licencia_windows" class="form-label">Licencia Windows</label>
                    <input type="text" class="form-control @error('licencia_windows') is-invalid @enderror" 
                           id="licencia_windows" name="licencia_windows" 
                           value="{{ old('licencia_windows', $equipment->licencia_windows ?? '') }}">
                    @error('licencia_windows')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="version_office" class="form-label">Versión Office</label>
                    <input type="text" class="form-control @error('version_office') is-invalid @enderror" 
                           id="version_office" name="version_office" 
                           value="{{ old('version_office', $equipment->version_office ?? '') }}">
                    @error('version_office')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="licencia_office" class="form-label">Licencia Office</label>
                    <input type="text" class="form-control @error('licencia_office') is-invalid @enderror" 
                           id="licencia_office" name="licencia_office" 
                           value="{{ old('licencia_office', $equipment->licencia_office ?? '') }}">
                    @error('licencia_office')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Información de Acceso Remoto -->
            <h5 class="mt-4 mb-3">Información de Acceso Remoto</h5>
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="id_anydesk" class="form-label">ID AnyDesk</label>
                    <input type="text" class="form-control @error('id_anydesk') is-invalid @enderror" 
                           id="id_anydesk" name="id_anydesk" 
                           value="{{ old('id_anydesk', $equipment->id_anydesk ?? '') }}">
                    @error('id_anydesk')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="pass_anydesk" class="form-label">Password AnyDesk</label>
                    <input type="text" class="form-control @error('pass_anydesk') is-invalid @enderror" 
                           id="pass_anydesk" name="pass_anydesk" 
                           value="{{ old('pass_anydesk', $equipment->pass_anydesk ?? '') }}">
                    @error('pass_anydesk')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="password_cuenta" class="form-label">Password Cuenta</label>
                    <input type="text" class="form-control @error('password_cuenta') is-invalid @enderror" 
                           id="password_cuenta" name="password_cuenta" 
                           value="{{ old('password_cuenta', $equipment->password_cuenta ?? '') }}">
                    @error('password_cuenta')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Observaciones y Comentarios -->
            <h5 class="mt-4 mb-3">Observaciones y Comentarios</h5>
            <div class="col-md-12">
                <div class="mb-3">
                    <label for="observaciones" class="form-label">Observaciones</label>
                    <textarea class="form-control @error('observaciones') is-invalid @enderror" 
                              id="observaciones" name="observaciones" rows="3">{{ old('observaciones', $equipment->observaciones ?? '') }}</textarea>
                    @error('observaciones')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-md-12">
                <div class="mb-3">
                    <label for="comentarios" class="form-label">Comentarios</label>
                    <textarea class="form-control @error('comentarios') is-invalid @enderror" 
                              id="comentarios" name="comentarios" rows="3">{{ old('comentarios', $equipment->comentarios ?? '') }}</textarea>
                    @error('comentarios')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="submit" class="btn btn-primary">Guardar Cambios</button>
    </div>
</form> 