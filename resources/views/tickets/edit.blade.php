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

                        {{-- Campo de Solución Aplicada --}}
                        <div class="mb-3" id="solucion-aplicada-group" style="display: none;">
                            <label for="solucion_aplicada" class="form-label">Solución Aplicada</label>
                            <textarea class="form-control @error('solucion_aplicada') is-invalid @enderror"
                                id="solucion_aplicada" name="solucion_aplicada" rows="4">{{ old('solucion_aplicada', $ticket->solucion_aplicada) }}</textarea>
                            @error('solucion_aplicada')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        @if(Auth::user()->role === 'user')
                        <div class="mb-3">
                            <label for="contact_email" class="form-label">Correo de contacto</label>
                            <input type="email" name="contact_email" class="form-control" value="{{ old('contact_email', $ticket->contact_email ?? '') }}" disabled>
                        </div>

                        <div class="mb-3">
                            <label for="contact_phone" class="form-label">Teléfono de contacto</label>
                            <input type="text" name="contact_phone" class="form-control" value="{{ old('contact_phone', $ticket->contact_phone ?? '') }}" disabled>
                        </div>
                        @endif
                        <hr>

                        <h4>Información del Equipo</h4>

                        <div class="mb-3">
                            <label class="form-label">Buscar Equipo Existente</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="equipmentSearch" placeholder="Buscar por número de serie, marca, modelo o usuario...">
                                <button class="btn btn-outline-secondary" type="button" id="searchButton">
                                    <i class="fas fa-search"></i>
                                </button>
                                <button class="btn btn-outline-danger" type="button" id="clearButton" style="display: none;">
                                    <i class="fas fa-times"></i> Limpiar
                                </button>
                            </div>
                            <div id="searchResults" class="list-group mt-2" style="display: none;"></div>
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="isNewEquipment" name="is_new_equipment" checked>
                                <label class="form-check-label" for="isNewEquipment">
                                    Es un equipo nuevo (no está en inventario)
                                </label>
                            </div>
                        </div>

                        <div id="equipmentFields">
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
                                    <div id="serialNumberExistsMessage" class="text-danger mt-1" style="display: none;">
                                        Este número de serie ya existe en el inventario.
                                    </div>
                                @error('numero_serie')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                    <label for="location_id" class="form-label">Centro</label>
                                <select class="form-select" id="location_id" name="location_id">
                                        <option value="">-- Selecciona un Centro --</option>
                                    @foreach($locations as $location)
                                    <option value="{{ $location->id }}" {{ $ticket->location_id == $location->id ? 'selected' : '' }}>
                                        {{ $location->name }}
                                    </option>
                                    @endforeach
                                </select>
                                </div>
                            </div>

                            <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="usuario" class="form-label">Usuario</label>
                                <input type="text" class="form-control @error('usuario') is-invalid @enderror"
                                    id="usuario" name="usuario" value="{{ old('usuario', $ticket->usuario) }}">
                                @error('usuario')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                    <label for="box_oficina" class="form-label">Box/Oficina</label>
                                    <input type="text" class="form-control @error('box_oficina') is-invalid @enderror"
                                        id="box_oficina" name="box_oficina" value="{{ old('box_oficina', $ticket->box_oficina) }}">
                                    @error('box_oficina')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            </div>
                            </div>

                        <input type="hidden" id="selected_equipment_id" name="equipment_inventory_id" value="{{ $ticket->equipment_inventory_id }}">

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

        console.log('toggleSolucionAplicada() ejecutada.');
        console.log('statusSelect:', statusSelect);
        console.log('solucionGroup:', solucionGroup);
        console.log('solucionField:', solucionField);

        let isResuelto = false;
        if (statusSelect) {
            const selectedOption = statusSelect.options[statusSelect.selectedIndex];
            isResuelto = selectedOption && selectedOption.text.trim().toLowerCase() === 'resuelto';
            console.log('selectedOption.text:', selectedOption ? selectedOption.text : 'N/A');
            console.log('isResuelto (después de comprobación):', isResuelto);
        }

        if (solucionGroup) {
        if (isResuelto) {
            solucionGroup.style.display = '';
            solucionField.setAttribute('required', 'required');
                console.log('Solución aplicada VISIBLE.');
        } else {
            solucionGroup.style.display = 'none';
            solucionField.removeAttribute('required');
                console.log('Solución aplicada OCULTA.');
            }
        } else {
            console.error('Elemento solucion-aplicada-group no encontrado.');
        }
    }
    document.addEventListener('DOMContentLoaded', function() {
        const statusSelect = document.getElementById('status_id');
        if (statusSelect) {
            statusSelect.addEventListener('change', toggleSolucionAplicada);
            console.log('Llamando a toggleSolucionAplicada() en DOMContentLoaded.');
            toggleSolucionAplicada(); // Llamada inicial al cargar la página
        }
    });

    document.addEventListener('DOMContentLoaded', function() {
        const serialNumberInput = document.getElementById('numero_serie');
        const serialNumberMessage = document.getElementById('serialNumberExistsMessage');
        let typingTimer;                // timer identifier
        const doneTypingInterval = 500; // time in ms (0.5 seconds)

        // On input, start the countdown
        serialNumberInput.addEventListener('input', () => {
            clearTimeout(typingTimer);
            if (serialNumberInput.value) {
                typingTimer = setTimeout(checkSerialNumber, doneTypingInterval);
            } else {
                serialNumberMessage.style.display = 'none';
                serialNumberInput.classList.remove('is-invalid');
                toggleEquipmentFields(false); // Habilitar campos si el número de serie está vacío
            }
        });

        function checkSerialNumber() {
            const serialNumber = serialNumberInput.value;
            // Obtener el número de serie actual del equipo asociado al ticket si existe
            const currentEquipmentSerialNumber = "{{ $ticket->equipmentInventory->numero_serie ?? null }}";

            // Solo verificar si el número de serie es diferente al del equipo actualmente asociado al ticket
            if (serialNumber && serialNumber !== currentEquipmentSerialNumber) {
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
                        toggleEquipmentFields(true); // Deshabilitar campos
                    } else {
                        serialNumberMessage.style.display = 'none';
                        serialNumberInput.classList.remove('is-invalid');
                        toggleEquipmentFields(false); // Habilitar campos
                    }
                })
                .catch(error => console.error('Error:', error));
            } else {
                serialNumberMessage.style.display = 'none';
                serialNumberInput.classList.remove('is-invalid');
                toggleEquipmentFields(false); // Habilitar campos si es el mismo serial o está vacío
            }
        }

        // Función para deshabilitar/habilitar los campos de información del equipo
        function toggleEquipmentFields(disable) {
            const fields = [
                'marca', 'modelo', 'location_id', 'usuario', 'ip_red_wifi',
                'cpu', 'ram', 'capacidad_almacenamiento', 'tarjeta_video',
                'id_anydesk', 'pass_anydesk', /* Agrega aquí los demás campos si es necesario */
                // Campos adicionales que no se identificaron en la primera lectura completa, pero pueden existir:
                'version_windows', 'licencia_windows', 'version_office', 'licencia_office',
                'password_cuenta', 'fecha_instalacion', 'comentarios', 'box_oficina'
            ];

            fields.forEach(fieldId => {
                const field = document.getElementById(fieldId);
                if (field) {
                    field.disabled = disable;
                }
            });
        }

        // Llamar a toggleEquipmentFields al cargar la página si el numero_serie ya tiene un valor
        // y si ese valor ya existe en el inventario (solo para edición, para reflejar el estado inicial)
        if (serialNumberInput.value) {
            const currentEquipmentSerialNumber = "{{ $ticket->equipmentInventory->numero_serie ?? null }}";
            if (serialNumberInput.value === currentEquipmentSerialNumber) {
                // Si el número de serie actual del ticket es el mismo que el del input,
                // no deshabilitar los campos, porque significa que es el equipo asociado.
                toggleEquipmentFields(false);
            } else {
                // Si el número de serie en el input es diferente al del equipo asociado (posiblemente un valor antiguo
                // o uno recién tipeado que no es el original), entonces ejecutar la verificación completa.
                checkSerialNumber();
            }
        }
    });

    document.addEventListener('DOMContentLoaded', function() {
        const equipmentFields = document.getElementById('equipmentFields');
        const isNewEquipment = document.getElementById('isNewEquipment');
        const searchResults = document.getElementById('searchResults');
        const equipmentSearch = document.getElementById('equipmentSearch');
        const searchButton = document.getElementById('searchButton');
        const clearButton = document.getElementById('clearButton');
        const selectedEquipmentId = document.getElementById('selected_equipment_id');
        let selectedEquipment = null;

        // Función para buscar equipos
        function searchEquipment() {
            const query = equipmentSearch.value;
            if (query.length < 3) return;

            fetch(`{{ route('equipment-inventory.search') }}?query=${encodeURIComponent(query)}`)
                .then(response => response.json())
                .then(data => {
                    searchResults.innerHTML = '';
                    if (data.length > 0) {
                        data.forEach(equipment => {
                            const item = document.createElement('a');
                            item.href = '#';
                            item.className = 'list-group-item list-group-item-action';
                            item.innerHTML = `
                                <div class="d-flex w-100 justify-content-between">
                                    <small class="mb-1">${equipment.marca} ${equipment.modelo}</small>
                                    <small>${equipment.numero_serie}</small>
                                </div>
                                <h6 class="mb-1">Usuario: ${equipment.usuario}</h6>
                                <div class="d-flex w-100 justify-content-between">
                                    <small>Centro: ${equipment.location ? equipment.location.name : 'No asignado'}</small>
                                    <small>Box/Oficina: ${equipment.box_oficina}</small>
                                </div>
                            `;
                            item.addEventListener('click', (e) => {
                                e.preventDefault();
                                selectEquipment(equipment);
                            });
                            searchResults.appendChild(item);
                        });
                        searchResults.style.display = 'block';
                    } else {
                        searchResults.innerHTML = '<div class="list-group-item">No se encontraron equipos</div>';
                        searchResults.style.display = 'block';
                        // Si no se encontró el equipo, marcar como nuevo
                        isNewEquipment.checked = true;
                        equipmentFields.style.display = 'block';
                        selectedEquipmentId.value = '';
                        selectedEquipment = null;
                        clearButton.style.display = 'none';
                    }
                });
        }

        // Función para seleccionar un equipo
        function selectEquipment(equipment) {
            selectedEquipment = equipment;
            selectedEquipmentId.value = equipment.id;
            isNewEquipment.checked = false;
            isNewEquipment.disabled = true; // Deshabilitar el checkbox
            equipmentFields.style.display = 'none';
            searchResults.style.display = 'none';
            equipmentSearch.value = `${equipment.marca} ${equipment.modelo} - ${equipment.numero_serie}`;
            clearButton.style.display = 'inline-block';
            searchButton.style.display = 'none'; // Ocultar el botón de búsqueda
        }

        // Función para limpiar la selección
        function clearEquipmentSelection() {
            selectedEquipment = null;
            selectedEquipmentId.value = '';
            isNewEquipment.checked = true;
            isNewEquipment.disabled = false;
            equipmentFields.style.display = 'block';
            equipmentSearch.value = '';
            clearButton.style.display = 'none';
            searchButton.style.display = 'inline-block'; // Mostrar el botón de búsqueda
            searchResults.style.display = 'none';
        }

        // Eventos
        searchButton.addEventListener('click', searchEquipment);
        equipmentSearch.addEventListener('keyup', (e) => {
            if (e.key === 'Enter') {
                searchEquipment();
            }
        });

        // Evento para el botón limpiar
        clearButton.addEventListener('click', clearEquipmentSelection);

        // Evento para el checkbox
        isNewEquipment.addEventListener('change', function() {
            if (selectedEquipment && !this.checked) {
                // Si hay un equipo seleccionado y se intenta desmarcar el checkbox
                this.checked = true;
                alert('No puedes desmarcar esta opción porque ya has seleccionado un equipo del inventario.');
                return;
            }
            
            if (this.checked) {
                clearEquipmentSelection();
            } else {
                // Si se desmarca el checkbox, intentar buscar el equipo por número de serie
                const numeroSerie = document.getElementById('numero_serie').value;
                if (numeroSerie) {
                    equipmentSearch.value = numeroSerie;
                    searchEquipment();
                }
            }
        });

        // Evento para el campo de número de serie
        document.getElementById('numero_serie').addEventListener('change', function() {
            if (this.value) {
                equipmentSearch.value = this.value;
                searchEquipment();
            }
        });

        // Inicializar estado
        if (selectedEquipmentId.value) {
            isNewEquipment.checked = false;
            isNewEquipment.disabled = true;
            equipmentFields.style.display = 'none';
            clearButton.style.display = 'inline-block';
            searchButton.style.display = 'none'; // Ocultar al inicializar si hay equipo seleccionado

            // Mostrar resumen del equipo seleccionado en el campo de búsqueda
            const equipo = {
                marca: "{{ $ticket->equipmentInventory->marca ?? '' }}",
                modelo: "{{ $ticket->equipmentInventory->modelo ?? '' }}",
                numero_serie: "{{ $ticket->equipmentInventory->numero_serie ?? '' }}"
            };
            if (equipo.marca || equipo.modelo || equipo.numero_serie) {
                equipmentSearch.value = `${equipo.marca} ${equipo.modelo} - ${equipo.numero_serie}`.trim();
            }
        }
    });
</script>
@endpush