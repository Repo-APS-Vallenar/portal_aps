@extends('layouts.app')

@section('content')
    <div class="container">
        <h2 class="mb-4">Gestión de Usuarios</h2>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <table class="table table-bordered table-striped">
            
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Correo</th>
                    <th>Rol</th>
                    <th>Estado</th>
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
                                    @if($user->is_active)
                                        <span class="badge bg-success">Activo</span>
                                    @else
                                        <span class="badge bg-danger">Deshabilitado</span>
                                    @endif
                                    @if($user->locked_until && now()->lessThan($user->locked_until))
                                        <span class="badge bg-danger">Bloqueado</span>
                                    @else
                                        <span class="badge bg-primary">Desbloqueado</span>
                                    @endif
                                </td>

                                <td>
                                    <div class="btn-group" role="group">
                                        <!-- Botón para editar solo si el usuario está activo -->
                                        @if($user->is_active)
                                            <a href="#" class="btn btn-outline-warning btn-sm" data-bs-toggle="modal"
                                                data-bs-target="#editUserModal{{ $user->id }}">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        @endif


                                        <!-- Formulario para habilitar/deshabilitar -->
                                        <form action="{{ route('users.toggle', $user) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit"
                                                class="btn btn-sm  {{ $user->is_active ? 'btn-outline-danger btn-no-rounded' : 'btn-outline-success' }}">
                                                {{ $user->is_active ? 'Deshabilitar' : 'Habilitar' }}
                                            </button>
                                        </form>


                                        <!-- Botón para abrir el modal de cambio de contraseña -->
                                        @if (
                                            (auth()->user()->role === 'superadmin' && $user->role !== 'superadmin') ||
                                            (auth()->user()->role === 'admin' && $user->role === 'user') ||
                                            (auth()->id() === $user->id)
                                        )
                                                                <button class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                                                    data-bs-target="#changePasswordModal{{ $user->id }}">
                                                                    Cambiar Contraseña
                                                                </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>

                @endforeach

                <!-- Modal para cambio de contraseña -->
                @foreach($users as $user)
                    <div class="modal fade" id="changePasswordModal{{ $user->id }}" tabindex="-1"
                        aria-labelledby="changePasswordModalLabel{{ $user->id }}" aria-hidden="true">
                        <div class="modal-dialog">
                            <form method="POST" action="{{ route('users.updatePassword', $user->id) }}">
                                @csrf
                                @method('PUT')
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="changePasswordModalLabel{{ $user->id }}">Cambiar Contraseña
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
                                        <button type="submit" class="btn btn-primary">Actualizar Contraseña</button>
                                        <button type="button" class="btn btn-secondary"
                                            data-bs-dismiss="modal">Cancelar</button>
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
                                                <h5 class="modal-title" id="editUserModalLabel{{ $user->id }}">Editar Usuario</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Cerrar"></button>
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
                                                            <option value="user" {{ $user->role === 'user' ? 'selected' : '' }}>Usuario
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
                                                <button type="button" class="btn btn-secondary"
                                                    data-bs-dismiss="modal">Cancelar</button>
                                                <button type="submit" class="btn btn-primary">Guardar cambios</button>
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
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Cerrar"></button>
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


            </tbody>
        </table>
    </div>
@endsection