@extends('layouts.app')

@section('content')
<div class="container py-1">
    <div class="row justify-content-center">
        <div class="col-lg-10 col-md-12 col-12">
            <div class="card shadow-sm rounded-4 mb-4 border border-2" style="background:#fff; max-width: 1100px; margin: 0 auto;">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-4 gap-3 flex-column flex-md-row text-center text-md-start">
                        <div class="profile-avatar bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mx-auto mx-md-0" style="width:70px;height:70px;font-size:2.2rem;">
                            <i class="bi bi-person-circle"></i>
                        </div>
                        <div>
                            <h2 class="mb-0">Mi Perfil</h2>
                            <div class="text-muted small">{{ $user->email }}</div>
                        </div>
                    </div>
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    <div class="row g-4 flex-column flex-md-row">
                        <div class="col-12 col-md-6 mb-3 mb-md-0">
                            <div class="card border border-2 shadow-sm h-100 profile-wide-card" style="background:#fff; padding: 2rem 1.5rem; border-radius: 1.5rem;">
                                <div class="card-body" style="width: 100%; max-width: 100%; margin: 0 auto; padding: 2rem 1.5rem;">
                                    <form method="POST" action="{{ route('profile.update') }}">
                                        @csrf
                                        <div class="mb-3">
                                            <label for="name" class="form-label">Nombre</label>
                                            <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="email" class="form-label">Correo</label>
                                            <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $user->email) }}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="phone" class="form-label">Teléfono</label>
                                            <input type="text" class="form-control" id="phone" name="phone" value="{{ old('phone', $user->phone) }}">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Rol</label>
                                            <input type="text" class="form-control" value="{{ $user->role === 'superadmin' ? 'Superadministrador' : ($user->role === 'admin' ? 'Administrador' : 'Usuario') }}" readonly>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Estado</label>
                                            <input type="text" class="form-control" value="{{ $user->is_active ? 'Activo' : 'Inactivo' }}" readonly>
                                        </div>
                                        <button type="submit" class="btn btn-primary w-100"><i class="bi bi-save me-1"></i>Actualizar Perfil</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-6 profile-col-right">
                            <div class="card border border-2 shadow-sm profile-wide-card" style="background:#fff; padding: 0rem 0rem; border-radius: 1.5rem; width: 100%; max-width: 100%;">
                                <div class="card-body" style="width: 100%; max-width: 100%;">
                                    <h5 class="card-title mb-3"><i class="bi bi-key me-2"></i>Cambiar Contraseña</h5>
                                    @if(session('error'))
                                        <div class="alert alert-danger">{{ session('error') }}</div>
                                    @endif
                                    <form method="POST" action="{{ route('profile.password') }}">
                                        @csrf
                                        <div class="mb-3">
                                            <label for="password" class="form-label">Nueva Contraseña</label>
                                            <input type="password" class="form-control" id="password" name="password" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="password_confirmation" class="form-label">Confirmar Contraseña</label>
                                            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                                        </div>
                                        <button type="submit" class="btn btn-success w-100"><i class="bi bi-shield-lock me-1"></i>Cambiar Contraseña</button>
                                    </form>
                                </div>
                            </div>
                            <div class="card border border-2 shadow-sm profile-wide-card" style="background:#fff; padding: 2rem 1.5rem; border-radius: 1.5rem;">
                                <div class="card-body">
                                    <h5 class="card-title mb-3"><i class="bi bi-clock-history me-2"></i>Actividad reciente</h5>
                                    <div class="d-none d-md-block">
                                        <ul class="list-group list-group-flush" style="max-height: 220px; overflow-y: auto;">
                                            @php
                                                $logs = \App\Models\AuditLog::where('user_id', $user->id)->latest()->limit(5)->get();
                                            @endphp
                                            @forelse($logs as $log)
                                                <li class="list-group-item small d-flex align-items-center gap-2">
                                                    <span class="text-muted" style="min-width:90px">{{ $log->created_at->format('d/m/Y H:i') }}</span>
                                                    <span class="badge bg-light text-dark border me-2">{{ $log->action }}</span>
                                                    <span>{{ $log->description }}</span>
                                                </li>
                                            @empty
                                                <li class="list-group-item text-muted">Sin actividad reciente.</li>
                                            @endforelse
                                        </ul>
                                    </div>
                                    <!-- Cards responsivas para móvil -->
                                    <div class="d-md-none">
                                        @forelse($logs as $log)
                                            <div class="audit-card mb-3 p-4 shadow-sm rounded border" style="border-radius: 1.3rem;">
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    <span class="badge bg-primary">{{ $log->created_at->format('d/m/Y H:i') }}</span>
                                                </div>
                                                <div class="mb-1"><strong>Acción:</strong> {{ $log->action }}</div>
                                                <div class="mb-2"><strong>Descripción:</strong> <span class="text-secondary">{{ $log->description }}</span></div>
                                            </div>
                                        @empty
                                            <div class="alert alert-info">Sin actividad reciente.</div>
                                        @endforelse
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    @media (min-width: 768px) {
        .profile-wide-card {
            max-width: 650px !important;
            margin-left: auto;
            margin-right: auto;
        }
        .profile-col-right {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            gap: 0.7rem;
        }
        .profile-col-right .card {
            width: 100%;
            margin-bottom: 0;
        }
    }
    @media (max-width: 767.98px) {
        .profile-wide-card,
        .profile-wide-card .card-body {
            max-width: 100% !important;
            width: 100% !important;
            box-sizing: border-box !important;
            padding-left: 0.8rem !important;
            padding-right: 0.8rem !important;
        }
    }
</style>
@endpush 