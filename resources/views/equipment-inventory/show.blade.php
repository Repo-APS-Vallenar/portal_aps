@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Detalles del Equipo</h5>
                    <div>
                        <a href="{{ route('equipment-inventory.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Volver
                        </a>
                        <a href="{{ route('equipment-inventory.edit', $equipmentInventory) }}" class="btn btn-warning">
                            <i class="fas fa-edit"></i> Editar
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <!-- Información Básica -->
                        <div class="col-12 col-md-6">
                            <h6 class="mb-3">Información Básica</h6>
                            <div class="table-responsive">
                                <table class="table table-bordered w-100">
                                    <tr>
                                        <th style="width: 30%">Marca</th>
                                        <td class="text-break">{{ $equipmentInventory->marca }}</td>
                                    </tr>
                                    <tr>
                                        <th>Modelo</th>
                                        <td class="text-break">{{ $equipmentInventory->modelo }}</td>
                                    </tr>
                                    <tr>
                                        <th>Número de Serie</th>
                                        <td class="text-break">{{ $equipmentInventory->numero_serie }}</td>
                                    </tr>
                                    <tr>
                                        <th>Ubicación</th>
                                        <td class="text-break">{{ $equipmentInventory->ubicacion }}</td>
                                    </tr>
                                    <tr>
                                        <th>Box/Oficina</th>
                                        <td class="text-break">{{ $equipmentInventory->box_oficina }}</td>
                                    </tr>
                                    <tr>
                                        <th>Usuario</th>
                                        <td class="text-break">{{ $equipmentInventory->usuario }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <!-- Especificaciones Técnicas -->
                        <div class="col-12 col-md-6">
                            <h6 class="mb-3">Especificaciones Técnicas</h6>
                            <div class="table-responsive">
                                <table class="table table-bordered w-100">
                                    <tr>
                                        <th style="width: 30%">IP/Red WiFi</th>
                                        <td class="text-break">{{ $equipmentInventory->ip_red_wifi }}</td>
                                    </tr>
                                    <tr>
                                        <th>CPU</th>
                                        <td class="text-break">{{ $equipmentInventory->cpu }}</td>
                                    </tr>
                                    <tr>
                                        <th>RAM</th>
                                        <td class="text-break">{{ $equipmentInventory->ram }}</td>
                                    </tr>
                                    <tr>
                                        <th>Capacidad Almacenamiento</th>
                                        <td class="text-break">{{ $equipmentInventory->capacidad_almacenamiento }}</td>
                                    </tr>
                                    <tr>
                                        <th>Tarjeta de Video</th>
                                        <td class="text-break">{{ $equipmentInventory->tarjeta_video }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <!-- Información de Software -->
                        <div class="col-12 col-md-6 mt-4">
                            <h6 class="mb-3">Información de Software</h6>
                            <div class="table-responsive">
                                <table class="table table-bordered w-100">
                                    <tr>
                                        <th style="width: 30%">Versión Windows</th>
                                        <td class="text-break">{{ $equipmentInventory->version_windows }}</td>
                                    </tr>
                                    <tr>
                                        <th>Licencia Windows</th>
                                        <td class="text-break">{{ $equipmentInventory->licencia_windows }}</td>
                                    </tr>
                                    <tr>
                                        <th>Versión Office</th>
                                        <td class="text-break">{{ $equipmentInventory->version_office }}</td>
                                    </tr>
                                    <tr>
                                        <th>Licencia Office</th>
                                        <td class="text-break">{{ $equipmentInventory->licencia_office }}</td>
                                    </tr>
                                    <tr>
                                        <th>Fecha Instalación</th>
                                        <td class="text-break">{{ $equipmentInventory->fecha_instalacion ? $equipmentInventory->fecha_instalacion->format('d/m/Y') : 'No especificada' }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <!-- Información de Acceso Remoto -->
                        <div class="col-12 col-md-6 mt-4">
                            <h6 class="mb-3">Información de Acceso Remoto</h6>
                            <div class="table-responsive">
                                <table class="table table-bordered w-100">
                                    <tr>
                                        <th style="width: 30%">ID AnyDesk</th>
                                        <td class="text-break">{{ $equipmentInventory->id_anydesk }}</td>
                                    </tr>
                                    <tr>
                                        <th>Password AnyDesk</th>
                                        <td class="text-break">{{ $equipmentInventory->pass_anydesk }}</td>
                                    </tr>
                                    <tr>
                                        <th>Password Cuenta</th>
                                        <td class="text-break">{{ $equipmentInventory->password_cuenta }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <!-- Comentarios -->
                        @if($equipmentInventory->comentarios)
                        <div class="col-12 mt-4">
                            <h6 class="mb-3">Comentarios</h6>
                            <div class="card">
                                <div class="card-body text-break">
                                    {{ $equipmentInventory->comentarios }}
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- Historial de Tickets -->
                        <div class="col-12 mt-4">
                            <h6 class="mb-3">Historial de Tickets</h6>
                            <div class="table-responsive">
                                <table class="table table-hover w-100">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Título</th>
                                            <th>Estado</th>
                                            <th>Fecha</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($equipmentInventory->tickets as $ticket)
                                            <tr>
                                                <td>#{{ $ticket->id }}</td>
                                                <td class="text-break">{{ $ticket->title }}</td>
                                                <td>
                                                    <span class="badge" style="background-color: {{ $ticket->status->color }}">
                                                        {{ $ticket->status->name }}
                                                    </span>
                                                </td>
                                                <td>{{ $ticket->created_at->format('d/m/Y H:i') }}</td>
                                                <td>
                                                    <a href="{{ route('tickets.show', $ticket) }}" class="btn btn-sm btn-info">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center">No hay tickets asociados</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Historial de Mantenimiento -->
                        <div class="col-12 mt-4">
                            <h6 class="mb-3">Historial de Mantenimiento</h6>
                            <div class="table-responsive">
                                <table class="table table-hover w-100">
                                    <thead>
                                        <tr>
                                            <th>Fecha</th>
                                            <th>Tipo</th>
                                            <th>Descripción</th>
                                            <th>Realizado Por</th>
                                            <th>Ticket Asociado</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($equipmentInventory->maintenanceLogs as $log)
                                            <tr>
                                                <td>{{ $log->maintenance_date->format('d/m/Y') }}</td>
                                                <td class="text-break">{{ $log->type_of_maintenance }}</td>
                                                <td class="text-break">{{ $log->description_of_work }}</td>
                                                <td class="text-break">{{ $log->user->name ?? 'N/A' }}</td>
                                                <td>
                                                    @if($log->ticket)
                                                        <a href="{{ route('tickets.show', $log->ticket) }}">#{{ $log->ticket->id }}</a>
                                                    @else
                                                        N/A
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center">No hay registros de mantenimiento para este equipo.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 