<div class="container-fluid">
    <div class="row">
        <!-- Información Básica -->
        <div class="col-12 col-md-6">
            <h6 class="mb-3">Información Básica</h6>
            <!-- Tabla para escritorio -->
            <div class="table-responsive d-none d-md-block">
                <table class="table table-bordered">
                    <tr>
                        <th style="width: 30%">Marca</th>
                        <td>{{ $equipment->marca }}</td>
                    </tr>
                    <tr>
                        <th>Modelo</th>
                        <td>{{ $equipment->modelo }}</td>
                    </tr>
                    <tr>
                        <th>Número de Serie</th>
                        <td>{{ $equipment->numero_serie }}</td>
                    </tr>
                    <tr>
                        <th>Tipo</th>
                        <td>{{ $equipment->tipo }}</td>
                    </tr>
                    <tr>
                        <th>Estado</th>
                        <td>{{ $equipment->estado }}</td>
                    </tr>
                    <tr>
                        <th>Usuario</th>
                        <td>{{ $equipment->usuario }}</td>
                    </tr>
                    <tr>
                        <th>Box/Oficina</th>
                        <td>{{ $equipment->box_oficina }}</td>
                    </tr>
                    <tr>
                        <th>Ubicación</th>
                        <td>{{ $equipment->location->name ?? 'N/A' }}</td>
                    </tr>
                </table>
            </div>
            <!-- Lista para móvil -->
            <ul class="list-group d-block d-md-none mb-3">
                <li class="list-group-item"><strong>Marca:</strong> {{ $equipment->marca }}</li>
                <li class="list-group-item"><strong>Modelo:</strong> {{ $equipment->modelo }}</li>
                <li class="list-group-item"><strong>Número de Serie:</strong> {{ $equipment->numero_serie }}</li>
                <li class="list-group-item"><strong>Tipo:</strong> {{ $equipment->tipo }}</li>
                <li class="list-group-item"><strong>Estado:</strong> {{ $equipment->estado }}</li>
                <li class="list-group-item"><strong>Usuario:</strong> {{ $equipment->usuario }}</li>
                <li class="list-group-item"><strong>Box/Oficina:</strong> {{ $equipment->box_oficina }}</li>
                <li class="list-group-item"><strong>Ubicación:</strong> {{ $equipment->location->name ?? 'N/A' }}</li>
            </ul>
        </div>

        <!-- Fechas -->
        <div class="col-12 col-md-6">
            <h6 class="mb-3">Fechas</h6>
            <!-- Tabla para escritorio -->
            <div class="table-responsive d-none d-md-block">
                <table class="table table-bordered">
                    <tr>
                        <th style="width: 30%">Fecha Adquisición</th>
                        <td>{{ $equipment->fecha_adquisicion ? $equipment->fecha_adquisicion->format('d/m/Y') : 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th>Último Mantenimiento</th>
                        <td>{{ $equipment->ultima_mantenimiento ? $equipment->ultima_mantenimiento->format('d/m/Y') : 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th>Próximo Mantenimiento</th>
                        <td>{{ $equipment->proximo_mantenimiento ? $equipment->proximo_mantenimiento->format('d/m/Y') : 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th>Fecha Instalación</th>
                        <td>{{ $equipment->fecha_instalacion ? $equipment->fecha_instalacion->format('d/m/Y') : 'N/A' }}</td>
                    </tr>
                </table>
            </div>
            <!-- Lista para móvil -->
            <ul class="list-group d-block d-md-none mb-3">
                <li class="list-group-item"><strong>Fecha Adquisición:</strong> {{ $equipment->fecha_adquisicion ? $equipment->fecha_adquisicion->format('d/m/Y') : 'N/A' }}</li>
                <li class="list-group-item"><strong>Último Mantenimiento:</strong> {{ $equipment->ultima_mantenimiento ? $equipment->ultima_mantenimiento->format('d/m/Y') : 'N/A' }}</li>
                <li class="list-group-item"><strong>Próximo Mantenimiento:</strong> {{ $equipment->proximo_mantenimiento ? $equipment->proximo_mantenimiento->format('d/m/Y') : 'N/A' }}</li>
                <li class="list-group-item"><strong>Fecha Instalación:</strong> {{ $equipment->fecha_instalacion ? $equipment->fecha_instalacion->format('d/m/Y') : 'N/A' }}</li>
            </ul>
        </div>

        <!-- Especificaciones Técnicas -->
        <div class="col-12 col-md-6 mt-4">
            <h6 class="mb-3">Especificaciones Técnicas</h6>
            <!-- Tabla para escritorio -->
            <div class="table-responsive d-none d-md-block">
                <table class="table table-bordered">
                    <tr>
                        <th style="width: 30%">IP/Red WiFi</th>
                        <td>{{ $equipment->ip_red_wifi ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th>CPU</th>
                        <td>{{ $equipment->cpu ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th>RAM</th>
                        <td>{{ $equipment->ram ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th>Capacidad Almacenamiento</th>
                        <td>{{ $equipment->capacidad_almacenamiento ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th>Tarjeta de Video</th>
                        <td>{{ $equipment->tarjeta_video ?? 'N/A' }}</td>
                    </tr>
                </table>
            </div>
            <!-- Lista para móvil -->
            <ul class="list-group d-block d-md-none mb-3">
                <li class="list-group-item"><strong>IP/Red WiFi:</strong> {{ $equipment->ip_red_wifi ?? 'N/A' }}</li>
                <li class="list-group-item"><strong>CPU:</strong> {{ $equipment->cpu ?? 'N/A' }}</li>
                <li class="list-group-item"><strong>RAM:</strong> {{ $equipment->ram ?? 'N/A' }}</li>
                <li class="list-group-item"><strong>Capacidad Almacenamiento:</strong> {{ $equipment->capacidad_almacenamiento ?? 'N/A' }}</li>
                <li class="list-group-item"><strong>Tarjeta de Video:</strong> {{ $equipment->tarjeta_video ?? 'N/A' }}</li>
            </ul>
        </div>

        <!-- Información de Software -->
        <div class="col-12 col-md-6 mt-4">
            <h6 class="mb-3">Información de Software</h6>
            <!-- Tabla para escritorio -->
            <div class="table-responsive d-none d-md-block">
                <table class="table table-bordered">
                    <tr>
                        <th style="width: 30%">Versión Windows</th>
                        <td>{{ $equipment->version_windows ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th>Licencia Windows</th>
                        <td>{{ $equipment->licencia_windows ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th>Versión Office</th>
                        <td>{{ $equipment->version_office ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th>Licencia Office</th>
                        <td>{{ $equipment->licencia_office ?? 'N/A' }}</td>
                    </tr>
                </table>
            </div>
            <!-- Lista para móvil -->
            <ul class="list-group d-block d-md-none mb-3">
                <li class="list-group-item"><strong>Versión Windows:</strong> {{ $equipment->version_windows ?? 'N/A' }}</li>
                <li class="list-group-item"><strong>Licencia Windows:</strong> {{ $equipment->licencia_windows ?? 'N/A' }}</li>
                <li class="list-group-item"><strong>Versión Office:</strong> {{ $equipment->version_office ?? 'N/A' }}</li>
                <li class="list-group-item"><strong>Licencia Office:</strong> {{ $equipment->licencia_office ?? 'N/A' }}</li>
            </ul>
        </div>

        <!-- Información de Acceso Remoto -->
        <div class="col-12 col-md-6 mt-4">
            <h6 class="mb-3">Información de Acceso Remoto</h6>
            <!-- Tabla para escritorio -->
            <div class="table-responsive d-none d-md-block">
                <table class="table table-bordered">
                    <tr>
                        <th style="width: 30%">ID AnyDesk</th>
                        <td>{{ $equipment->id_anydesk ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th>Password AnyDesk</th>
                        <td>{{ $equipment->pass_anydesk ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th>Password Cuenta</th>
                        <td>{{ $equipment->password_cuenta ?? 'N/A' }}</td>
                    </tr>
                </table>
            </div>
            <!-- Lista para móvil -->
            <ul class="list-group d-block d-md-none mb-3">
                <li class="list-group-item"><strong>ID AnyDesk:</strong> {{ $equipment->id_anydesk ?? 'N/A' }}</li>
                <li class="list-group-item"><strong>Password AnyDesk:</strong> {{ $equipment->pass_anydesk ?? 'N/A' }}</li>
                <li class="list-group-item"><strong>Password Cuenta:</strong> {{ $equipment->password_cuenta ?? 'N/A' }}</li>
            </ul>
        </div>

        <!-- Observaciones y Comentarios -->
        @if($equipment->observaciones || $equipment->comentarios)
        <div class="col-12 mt-4">
            <h6 class="mb-3">Notas Adicionales</h6>
            @if($equipment->observaciones)
            <div class="card mb-3">
                <div class="card-header">Observaciones</div>
                <div class="card-body">
                    {{ $equipment->observaciones }}
                </div>
            </div>
            @endif
            @if($equipment->comentarios)
            <div class="card">
                <div class="card-header">Comentarios</div>
                <div class="card-body">
                    {{ $equipment->comentarios }}
                </div>
            </div>
            @endif
        </div>
        @endif
    </div>
</div> 