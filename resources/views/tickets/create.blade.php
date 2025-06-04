@extends('layouts.app')

@section('title', 'Nuevo Ticket')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h2 class="mb-0">Nuevo Ticket</h2>
                </div>

                <div class="card-body">
                    <form method="POST" action="{{ route('tickets.store') }}" enctype="multipart/form-data">
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

                        <div class="mb-3">
                            <div class="col-md-12 mb-3">
                                <label for="title" class="form-label">Nombre del funcionario (Quien hace la solicitud)</label>
                                <input type="text" class="form-control @error('title') is-invalid @enderror" id="title"
                                    name="title" value="{{ old('title') }}" required>
                                @error('title')
                                <div class="invalid-feedback">{{ $errors->first('contact_email') }}</div>
                                @enderror
                            </div>

                            <div class="col-md-12">
                                <label for="category_id" class="form-label">Categoría</label>
                                <select class="form-select @error('category_id') is-invalid @enderror" id="category_id"
                                    name="category_id" required>
                                    <option value="">Selecciona una categoría</option>
                                    @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                <div class="invalid-feedback">{{ $errors->first('status_id') }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="col-md-12 mb-3">
                                <label for="priority" class="form-label">Prioridad</label>
                                <select class="form-select @error('priority') is-invalid @enderror" id="priority"
                                    name="priority" required>
                                    <option value="baja" {{ old('priority') == 'baja' ? 'selected' : '' }}>Baja</option>
                                    <option value="media" {{ old('priority') == 'media' ? 'selected' : '' }}>Media
                                    </option>
                                    <option value="alta" {{ old('priority') == 'alta' ? 'selected' : '' }}>Alta</option>
                                    <option value="urgente" {{ old('priority') == 'urgente' ? 'selected' : '' }}>Urgente
                                    </option>
                                </select>
                                @error('priority')
                                <div class="invalid-feedback">{{ $errors->first('priority') }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <div class="col-md-12">
                                    @if(isset($statusSolicitado))
                                    <label for="status_id" class="form-label">Estado</label>
                                    <input type="text" class="form-control" value="Solicitado" disabled>
                                    <input type="hidden" name="status_id" value="{{ $statusSolicitado->id }}">
                                    @elseif(isset($statuses))
                                    <label for="status_id" class="form-label">Estado</label>
                                    <select class="form-select @error('status_id') is-invalid @enderror"
                                        name="status_id" id="status_id" required>
                                        <option value="">Selecciona un estado</option>
                                        @foreach($statuses as $status)
                                        <option value="{{ $status->id }}" {{ old('status_id') == $status->id ? 'selected' : '' }}>
                                            {{ $status->name }}
                                        </option>
                                        @endforeach
                                    </select>
                                    @error('status_id')
                                    <div class="invalid-feedback">{{ $errors->first('status_id') }}</div>
                                    @enderror
                                    @endif
                                </div>
                            </div>

                            <!--<div class="mb-3">
                                    <div class="col-md-12">
                                        <label for="due_date" class="form-label">Fecha límite</label>
                                        <input type="datetime-local"
                                            class="form-control @error('due_date') is-invalid @enderror" id="due_date"
                                            name="due_date" value="{{ old('due_date') }}">
                                        @error('due_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                -->

                            <h4 class="mb-3">Información del Equipo</h4>
                            <div class="col-md-12 mb-3">
                                <label for="description" class="form-label">Descripción del problema</label>
                                <textarea class="form-control @error('description') is-invalid @enderror"
                                    id="description" name="description" rows="3" style="resize: none;">{{ old('description') }}</textarea>
                                @error('description')
                                <div class="invalid-feedback">{{ $errors->first('description') }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                @if(Auth::user()->isAdmin())
                                <div class="col-md-12">
                                    <label for="marca" class="form-label">Marca</label>
                                    <input type="text" class="form-control @error('marca') is-invalid @enderror"
                                        id="marca" name="marca" value="{{ old('marca') }}">
                                    @error('marca')
                                    <div class="invalid-feedback">{{ $errors->first('marca') }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-12">
                                    <label for="modelo" class="form-label">Modelo</label>
                                    <input type="text" class="form-control @error('modelo') is-invalid @enderror"
                                        id="modelo" name="modelo" value="{{ old('modelo') }}">
                                    @error('modelo')
                                    <div class="invalid-feedback">{{ $errors->first('modelo') }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-12">
                                    <label for="numero_serie" class="form-label">Número de Serie</label>
                                    <input type="text" class="form-control @error('numero_serie') is-invalid @enderror"
                                        id="numero_serie" name="numero_serie" value="{{ old('numero_serie') }}">
                                    @error('numero_serie')
                                    <div class="invalid-feedback">{{ $errors->first('numero_serie') }}</div>
                                    @enderror
                                </div>
                                @endif
                            </div>

                            <div class="mb-3">
                                <div class="col-md-12 mb-3">
                                    <label for="location_id" class="form-label">Ubicación</label>
                                    <select class="form-select @error('location_id') is-invalid @enderror" id="location_id" name="location_id" required>
                                        <option value="">Selecciona una ubicación</option>
                                        @foreach($locations as $location)
                                        <option value="{{ $location->id }}" {{ old('location_id') == $location->id ? 'selected' : '' }}>
                                            {{ $location->name }}
                                        </option>
                                        @endforeach
                                    </select>
                                    @error('location_id')
                                    <div class="invalid-feedback">{{ $errors->first('location_id') }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-12">
                                    <label for="usuario" class="form-label">Usuario (Quien utiliza el equipo)</label>
                                    <input type="text" class="form-control @error('usuario') is-invalid @enderror"
                                        id="usuario" name="usuario" value="{{ old('usuario') }}">
                                    @error('usuario')
                                    <div class="invalid-feedback">{{ $errors->first('usuario') }}</div>
                                    @enderror
                                </div>
                            </div>


                            @if(Auth::user()->isAdmin())

                            <div class="mb-3">
                                <div class="col-md-12">
                                    <label for="ip_red_wifi" class="form-label">IP/Red WiFi</label>
                                    <input type="text" class="form-control @error('ip_red_wifi') is-invalid @enderror"
                                        id="ip_red_wifi" name="ip_red_wifi" value="{{ old('ip_red_wifi') }}">
                                    @error('ip_red_wifi')
                                    <div class="invalid-feedback">{{ $errors->first('ip_red_wifi') }}</div>
                                    @enderror
                                </div>
                            </div>

                            <h4 class="mb-3">Especificaciones Técnicas</h4>
                            <div class="mb-3">
                                <div class="col-md-12">
                                    <label for="cpu" class="form-label">CPU</label>
                                    <input type="text" class="form-control @error('cpu') is-invalid @enderror" id="cpu"
                                        name="cpu" value="{{ old('cpu') }}">
                                    @error('cpu')
                                    <div class="invalid-feedback">{{ $errors->first('cpu') }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-12">
                                    <label for="ram" class="form-label">RAM</label>
                                    <input type="text" class="form-control @error('ram') is-invalid @enderror" id="ram"
                                        name="ram" value="{{ old('ram') }}">
                                    @error('ram')
                                    <div class="invalid-feedback">{{ $errors->first('ram') }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="col-md-12">
                                    <label for="capacidad_almacenamiento" class="form-label">Capacidad de
                                        Almacenamiento</label>
                                    <input type="text"
                                        class="form-control @error('capacidad_almacenamiento') is-invalid @enderror"
                                        id="capacidad_almacenamiento" name="capacidad_almacenamiento"
                                        value="{{ old('capacidad_almacenamiento') }}">
                                    @error('capacidad_almacenamiento')
                                    <div class="invalid-feedback">{{ $errors->first('capacidad_almacenamiento') }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-12">
                                    <label for="tarjeta_video" class="form-label">Tarjeta de Video</label>
                                    <input type="text" class="form-control @error('tarjeta_video') is-invalid @enderror"
                                        id="tarjeta_video" name="tarjeta_video" value="{{ old('tarjeta_video') }}">
                                    @error('tarjeta_video')
                                    <div class="invalid-feedback">{{ $errors->first('tarjeta_video') }}</div>
                                    @enderror
                                </div>
                            </div>
                            @endif
                            @if(Auth::user()->isAdmin())
                            <h4 class="mb-3">Información de Acceso Remoto</h4>

                            <div class="mb-3">
                                <div class="col-md-12">
                                    <label for="id_anydesk" class="form-label">ID AnyDesk</label>
                                    <input type="text" class="form-control @error('id_anydesk') is-invalid @enderror"
                                        id="id_anydesk" name="id_anydesk" value="{{ old('id_anydesk') }}">
                                    @error('id_anydesk')
                                    <div class="invalid-feedback">{{ $errors->first('id_anydesk') }}</div>
                                    @enderror
                                </div>
                            </div>
                            @endif
                            @if(Auth::user()->isAdmin())
                            <div class="col-md-12 mb-3">
                                <label for="fecha_instalacion" class="form-label">Fecha de Reinstalación</label>
                                <input type="date" class="form-control @error('fecha_instalacion') is-invalid @enderror"
                                    id="fecha_instalacion" name="fecha_instalacion"
                                    value="{{ old('fecha_instalacion') }}">
                                    @error('fecha_instalacion')
                                    <div class="invalid-feedback">{{ $errors->first('fecha_instalacion') }}</div>
                                    @enderror
                            </div>
                            @endif
                            <div class="mb-3">
                                <label for="contact_email" class="form-label">Correo de contacto</label>
                                <input type="email" name="contact_email" class="form-control"
                                    value="{{ old('contact_email', auth()->user()->email) }}"
                                    @if(auth()->user()->role === 'usuario') readonly @endif
                                >
                            </div>

                            <div class="mb-3">
                                <label for="contact_phone" class="form-label">Teléfono de contacto</label>
                                <input type="text" name="contact_phone" class="form-control" value="{{ old('contact_phone', $ticket->contact_phone ?? '') }}">
                            </div>

                            @if(auth()->user()->role === 'user')
                            <div class="mb-3">
                                <label for="attachments" class="form-label">Adjuntar archivos (opcional)</label>
                                <input type="file" class="form-control" id="attachments" name="attachments[]" multiple accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png">
                                <div class="form-text">Puedes adjuntar varios archivos. Tamaño máximo por archivo: 10MB.</div>
                            </div>
                            @endif

                            @if(Auth::user()->isAdmin())
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <label for="comentarios" class="form-label">Comentarios sobre el ticket (Que tareas se realizaron, que se hizo para la solucion, etc)</label>
                                    <textarea class="form-control @error('comentarios') is-invalid @enderror"
                                        id="comentarios" name="comentarios" rows="3">{{ old('comentarios') }}</textarea>
                                    @if ($errors->has('comentarios'))
                                    <div class="invalid-feedback">{{ $errors->first('comentarios') }}</div>
                                    @endif
                                </div>
                            </div>
                            @endif
                            <!-- Barra de progreso de carga -->
                            <div id="uploadProgressBarContainer" style="display:none; width:100%; margin-bottom: 10px;">
                                <div class="progress" style="height: 18px;">
                                    <div id="uploadProgressBar" class="progress-bar progress-bar-striped progress-bar-animated bg-primary" role="progressbar" style="width: 0%; font-weight:600; font-size:1em;">0%</div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-between">
                                <a href="{{ route('tickets.index') }}" class="btn btn-secondary">Cancelar</a>
                                <button type="submit" class="btn btn-gradient">Crear Ticket</button>
                            </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
{{-- @include('tickets.partials.document-upload') --}}

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form[action="{{ route('tickets.store') }}"]');
    const progressBarContainer = document.getElementById('uploadProgressBarContainer');
    const progressBar = document.getElementById('uploadProgressBar');
    const fileInput = document.getElementById('attachments');
    if (form && progressBarContainer && progressBar) {
        form.addEventListener('submit', function(e) {
            if (fileInput && fileInput.files.length > 0) {
                progressBarContainer.style.display = 'block';
                progressBar.style.width = '0%';
                progressBar.innerText = '0%';
                let percent = 0;
                const interval = setInterval(() => {
                    if (percent < 95) {
                        percent += 5;
                        progressBar.style.width = percent + '%';
                        progressBar.innerText = percent + '%';
                    } else {
                        clearInterval(interval);
                    }
                }, 100);
            }
        });
        window.updateDocumentsSection = (function(orig) {
            return function() {
                if (progressBarContainer) {
                    progressBarContainer.style.display = 'none';
                }
                if (orig) orig.apply(this, arguments);
            };
        })(window.updateDocumentsSection);
    }
});
</script>
@endsection