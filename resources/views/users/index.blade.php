@extends('layouts.app')

@section('title', 'Usuarios')

@section('content')
    <div class="container">
        <h2 class="mb-4">Gestión de Usuarios</h2>
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert" id="success-alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <script>
                setTimeout(function () {
                    var alert = document.getElementById('success-alert');
                    if (alert) {
                        alert.classList.remove('show');
                        alert.classList.add('fade');
                        // Esperamos que la animación de desvanecimiento termine antes de eliminarla
                        setTimeout(function () {
                            alert.remove();
                        }, 150); // Espera el tiempo de la animación de desvanecimiento
                    }
                }, 5000); // 5000 milisegundos (5 segundos)
            </script>
        @endif
        <div class="card mb-4 shadow-sm">
            <div class="card-body">
                <div class="row align-items-center g-2">
                    <div class="col-md-6">
                        <form method="GET" action="{{ route('users.index') }}" class="mb-3">
                            <div class="input-group">
                                <input type="text" name="search" class="form-control"
                                    placeholder="Buscar por nombre o correo..." value="{{ request('search') }}">
                                <button class="btn btn-primary" type="submit">Buscar</button>
                                @if(request('search'))
                                    <a href="{{ route('users.index') }}" class="btn btn-secondary">Limpiar</a>
                                @endif
                            </div>
                        </form>
                    </div>
                    <div class="col-md-6 text-md-end d-flex justify-content-end gap-2">
                        <div class="d-flex gap-2">
                            <a href="{{ route('users.export.excel', ['search' => request('search')]) }}"
                                class="btn btn-success"><i class="bi bi-file-earmark-excel me-1"></i>Exportar a Excel</a>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ route('users.exports.pdf', ['search' => request('search')]) }}"
                                class="btn btn-danger"><i class="bi bi-file-earmark-pdf me-1"></i> Exportar a PDF</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        @php
                            $columns = [
                                'name' => 'Nombre',
                                'email' => 'Correo',
                                'role' => 'Rol',
                                'is_blocked' => 'Estado',
                            ];
                            $currentSort = request('sort', 'name');
                            $currentDirection = request('direction', 'asc');
                        @endphp
                        @foreach($columns as $col => $label)
                            <th>
                                @php
                                    $newDirection = ($currentSort === $col && $currentDirection === 'asc') ? 'desc' : 'asc';
                                    $icon = '';
                                    if ($currentSort === $col) {
                                        $icon = $currentDirection === 'asc' ? '↑' : '↓';
                                    }
                                @endphp
                                <a href="{{ request()->fullUrlWithQuery(['sort' => $col, 'direction' => $newDirection, 'page' => null]) }}" style="text-decoration:none; color:inherit;">
                                    {{ $label }} {!! $icon !!}
                                </a>
                            </th>
                        @endforeach
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                        <tr class="{{ $user->is_blocked ? 'user-blocked' : '' }}">
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>
                                @if ($user->role === 'superadmin')
                                    Superadministrador
                                @elseif ($user->role === 'admin')
                                    Administrador
                                @else
                                    Usuario
                                @endif
                            </td>
                            <td>
                                @if($user->locked_until && now()->lessThan($user->locked_until))
                                    <span class="badge bg-danger">Bloqueado</span>
                                @else
                                    <span class="badge bg-primary">Desbloqueado</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    @if($user->is_active)
                                        <a href="#" class="btn btn-warning btn-sm" data-bs-toggle="modal"
                                            data-bs-target="#editUserModal{{ $user->id }}">
                                            <i class="fas fa-edit"> </i>Modificar
                                        </a>
                                    @endif
                                    @if (
                                            (auth()->user()->role === 'superadmin' && $user->role !== 'superadmin') ||
                                            (auth()->user()->role === 'admin' && $user->role === 'user') ||
                                            (auth()->id() === $user->id)
                                        )
                                        <button class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                            data-bs-target="#changePasswordModal{{ $user->id }}">
                                            Cambiar Contraseña
                                            <i class="fas fa-key"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Cards para usuarios en móvil --}}
        <div class="user-card-list d-md-none">
            @foreach($users as $user)
                <div class="user-card" style="position:relative;">
                    <div style="display:flex;align-items:center;gap:0.7em;">
                        <span class="badge bg-primary" style="font-size:1em;padding:0.22rem 1.1rem;border-radius:12px;">
                            #{{ $user->id }}
                        </span>
                        <span
                            class="small text-secondary">{{ $user->created_at ? $user->created_at->format('d/m/Y H:i') : '' }}</span>
                    </div>
                    <div class="user-card-row">
                        <span class="user-card-label">Nombre:</span>
                        <span class="fw-bold">{{ $user->name }}</span>
                    </div>
                    <div class="user-card-row">
                        <span class="user-card-label">Correo:</span>
                        <span>{{ $user->email }}</span>
                    </div>
                    <div class="user-card-row">
                        <span class="user-card-label">Rol:</span>
                        <span>
                            @if ($user->role === 'superadmin')
                                <span class="badge bg-secondary" style="background:#322f6c !important;">Superadmin</span>
                            @elseif ($user->role === 'admin')
                                <span class="badge bg-accent">Admin</span>
                            @else
                                <span class="badge bg-primary">Usuario</span>
                            @endif
                        </span>
                    </div>
                    <div class="user-card-row">
                        <span class="user-card-label">Estado:</span>
                        <span>
                            @if($user->locked_until && now()->lessThan($user->locked_until))
                                <span class="badge bg-danger">Bloqueado</span>
                            @else
                                <span class="badge bg-primary">Desbloqueado</span>
                            @endif
                        </span>
                    </div>
                    <div class="user-card-actions ticket-card-actions" style="gap:0.6rem;">
                        @php
                            $puedeEditar = $user->is_active;
                            $puedeCambiarPass = $user->is_active && ((auth()->user()->role === 'superadmin' && $user->role !== 'superadmin') || (auth()->user()->role === 'admin' && $user->role === 'user') || (auth()->id() === $user->id));
                        @endphp
                        @if($puedeEditar && $puedeCambiarPass)
                            <a href="#" class="btn btn-warning btn-sm ticket-pill-btn flex-fill" data-bs-toggle="modal"
                                data-bs-target="#editUserModal{{ $user->id }}">
                                <i class="fas fa-edit"></i> Editar
                            </a>
                            <button class="btn btn-primary btn-sm ticket-pill-btn flex-fill" data-bs-toggle="modal"
                                data-bs-target="#changePasswordModal{{ $user->id }}">
                                <i class="fas fa-key"></i> Contraseña
                            </button>
                        @elseif($puedeEditar)
                            <a href="#" class="btn btn-warning btn-sm ticket-pill-btn w-100" data-bs-toggle="modal"
                                data-bs-target="#editUserModal{{ $user->id }}">
                                <i class="fas fa-edit"></i> Editar
                            </a>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Modales globales para todos los usuarios --}}
        @foreach($users as $user)
            <!-- Modal para cambio de contraseña -->
            <div class="modal fade" id="changePasswordModal{{ $user->id }}" tabindex="-1"
                aria-labelledby="changePasswordModalLabel{{ $user->id }}" aria-hidden="true">
                <div class="modal-dialog">
                    <form method="POST" action="{{ route('users.updatePassword', $user->id) }}">
                        @csrf
                        @method('PUT')
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="changePasswordModalLabel{{ $user->id }}">Cambiar
                                    Contraseña
                                    de {{ $user->name }}</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label for="new-password-{{ $user->id }}" class="form-label">Nueva
                                        Contraseña</label>
                                    <input type="password" class="form-control" name="password"
                                        id="new-password-{{ $user->id }}" required>
                                </div>
                                <div class="mb-3">
                                    <label for="confirm-password-{{ $user->id }}" class="form-label">Confirmar
                                        Contraseña</label>
                                    <input type="password" class="form-control" name="password_confirmation"
                                        id="confirm-password-{{ $user->id }}" required>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                <button type="submit" class="btn btn-gradient">Actualizar Contraseña</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        @endforeach

        @foreach($users as $user)
            <!-- Modal de edición -->
            @php
                $authUser = Auth::user();
            @endphp
            <div class="modal fade" id="editUserModal{{ $user->id }}" tabindex="-1"
                aria-labelledby="editUserModalLabel{{ $user->id }}" aria-hidden="true">
                <div class="modal-dialog">
                    <form method="POST" action="{{ route('users.update', $user) }}">
                        @csrf
                        @method('PUT')
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="editUserModalLabel{{ $user->id }}">Editar
                                    Usuario</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                            </div>
                            <div class="modal-body">
                                <!-- Campos del formulario -->
                                <div class="mb-3">
                                    <label for="name{{ $user->id }}" class="form-label">Nombre</label>
                                    <input type="text" class="form-control" id="name{{ $user->id }}" name="name"
                                        value="{{ $user->name }}" required>
                                </div>
                                <div class="mb-3">
                                    <label for="email{{ $user->id }}" class="form-label">Correo</label>
                                    <input type="email" class="form-control" id="email{{ $user->id }}" name="email"
                                        value="{{ $user->email }}" required>
                                </div>
                                @if ($authUser && $authUser->role === 'superadmin')
                                    <div class="mb-3">
                                        <label for="role-{{ $user->id }}" class="form-label">Rol</label>
                                        <select class="form-select" name="role" id="role-{{ $user->id }}">
                                            <option value="user" {{ $user->role === 'user' ? 'selected' : '' }}>
                                                Usuario
                                            </option>
                                            <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>
                                                Administrador</option>
                                            <option value="superadmin" {{ $user->role === 'superadmin' ? 'selected' : '' }}>
                                                Superadministrador</option>
                                        </select>
                                    </div>
                                @endif
                                <!-- Agrega aquí los campos adicionales (rol, teléfono, contraseña) si deseas -->
                                @if(auth()->user()->role === 'superadmin' || auth()->user()->role === 'admin')
                                    @if($user->locked_until && now()->lessThan($user->locked_until))
                                        <div class="form-group mt-3">
                                            <label>
                                                <input type="checkbox" name="unlock_account" value="1">
                                                Desbloquear cuenta
                                            </label>
                                        </div>
                                    @endif
                                @endif
                                @if(auth()->user()->role === 'superadmin' && (!($user->locked_until && now()->lessThan($user->locked_until))))
                                    <div class="form-group mt-2">
                                        <label for="lock_user" class="form-label">Bloquear usuario manualmente</label>
                                        <input type="checkbox" name="lock_user" id="lock_user">

                                    </div>
                                @elseif(auth()->user()->role === 'superadmin')
                                    <div class="alert alert-warning mt-2">
                                        Este usuario está bloqueado hasta
                                        {{ \Carbon\Carbon::parse($user->locked_until)->format('d/m/Y H:i') }}.
                                    </div>
                                @endif

                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                <button type="submit" class="btn btn-gradient">Guardar cambios</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        @endforeach

        <!-- Modal de error -->
        <div class="modal fade" id="modalError" tabindex="-1" aria-labelledby="modalErrorLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content border-danger">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title" id="modalErrorLabel">Acción no permitida</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body">
                        {{ session('modal_error') }}
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>

        @if(session('modal_error'))
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    const errorModal = new bootstrap.Modal(document.getElementById('modalError'));
                    errorModal.show();
                });
            </script>
        @endif
    </div>
@endsection