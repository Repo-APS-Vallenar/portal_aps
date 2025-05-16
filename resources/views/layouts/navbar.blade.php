<!-- Navbar fijo -->
<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm fixed-top">
    <div class="container-fluid px-5">
        <a class="navbar-brand d-flex align-items-center">
            <img src="{{ asset('images/logo_vallenar.png') }}" alt="Logo" class="img-fluid me-2"
                style="max-height: 50px;">
            <div class="intranet-logo-gradient">
                INTRANET APS
            </div>
        </a>
        <button class="navbar-toggler navbar-toggler-right" type="button" data-bs-toggle="collapse"
            data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false"
            aria-label="{{ __('Toggle navigation') }}">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <!-- Left Side Of Navbar -->
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('/') ? 'active' : '' }}" href="{{ url('/') }}">
                        <i class="bi bi-house-door menu-icon"></i> Inicio
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('platforms*') ? 'active' : '' }}"
                        href="{{ route('platforms.index') }}">
                        <i class="bi bi-grid menu-icon"></i> Plataformas
                    </a>
                </li>
                @auth
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('tickets*') ? 'active' : '' }}"
                            href="{{ route('tickets.index') }}">
                            <i class="bi bi-ticket-detailed menu-icon"></i> Tickets
                        </a>
                    </li>
                @endauth
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('contacto*') ? 'active' : '' }}" href="{{ route('contacto') }}">
                        <i class="bi bi-envelope menu-icon"></i> Contacto
                    </a>
                </li>
                @auth
                    @if (auth()->user()->role === 'superadmin')
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('audit.index') }}">
                                <i class="bi bi-shield-check menu-icon"></i> Auditoría
                            </a>
                        </li>
                    @endif
                @endauth
                @auth
                    <li class="nav-item dropdown">
                        <a id="userDropdown" class="nav-link dropdown-toggle d-flex align-items-center" href="#"
                            role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="bi bi-person-circle me-2" style="font-size:1.5rem;color:#01a3d5;"></i>
                            <span>{{ Auth::user()->name }}</span>
                        </a>
                        <div class="dropdown-menu user-dropdown-menu" aria-labelledby="userDropdown"
                            style="min-width: 220px;">
                            <div class="user-dropdown-header d-flex flex-column align-items-center p-3 mb-2">
                                <div class="user-avatar mb-2">
                                    <i class="bi bi-person-circle" style="font-size:2.2rem;color:#01a3d5;"></i>
                                </div>
                                <div class="fw-bold" style="font-size:1.1rem;">{{ Auth::user()->name }}</div>
                                <div class="text-secondary small">
                                    {{ Auth::user()->role === 'superadmin' ? 'Superadministrador' : (Auth::user()->role === 'admin' ? 'Administrador' : 'Usuario') }}
                                </div>
                            </div>
                            <div class="dropdown-divider my-1"></div>
                            <a class="dropdown-item user-dropdown-item" href="{{ route('profile') }}">
                                <i class="bi bi-person-lines-fill me-2 text-secondary"></i> Mi perfil
                            </a>
                            @if (Auth::user()->role !== 'user')
                                <a class="dropdown-item user-dropdown-item" href="{{ route('users.index') }}">
                                    <i class="bi bi-people-fill me-2 text-primary"></i> Lista de Usuarios
                                </a>
                            @endif
                            @if (Auth::user()->role === 'superadmin' || Auth::user()->role === 'admin')
                                <a class="dropdown-item user-dropdown-item" href="{{ route('users.create') }}">
                                    <i class="bi bi-person-plus-fill me-2 text-success"></i> Registrar Usuario
                                </a>
                            @endif
                            @if(auth()->user()->role === 'admin' || auth()->user()->role === 'superadmin')
                                <a class="dropdown-item user-dropdown-item" href="{{ route('admin.parameters') }}">
                                    <i class="bi bi-gear me-2 text-warning"></i> Parámetros del sistema
                                </a>
                            @endif
                            <a class="dropdown-item user-dropdown-item user-logout text-danger" href="{{ route('logout') }}"
                                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="bi bi-box-arrow-right me-2 text-danger"></i> Cerrar Sesión
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                        </div>
                    </li>
                @endauth
            </ul>

            <!-- Right Side Of Navbar -->
            <ul class="navbar-nav ms-auto">
                @auth
                    {{-- <x-notification-bell /> --}}
                @endauth
            </ul>
        </div>
    </div>
</nav>

@push('styles')
    <style>
        @media (max-width: 991.98px) {
            .user-dropdown-menu {
                position: fixed !important;
                top: 50% !important;
                left: 50% !important;
                transform: translate(-50%, -50%) !important;
                width: 90% !important;
                max-width: 320px !important;
                height: auto !important;
                max-height: 80vh !important;
                border-radius: 12px !important;
                box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15) !important;
                background: white !important;
                margin: 0 !important;
                padding: 1rem !important;
                z-index: 1050 !important;
                display: block !important;
            }

            .user-dropdown-menu::before {
                content: '';
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: rgba(0, 0, 0, 0.5);
                z-index: -1;
            }

            .user-dropdown-scrollable {
                max-height: 50vh;
                overflow-y: auto;
            }

            .user-dropdown-logout-fixed {
                border-top: 1px solid #eee;
                padding-top: 0.7rem;
                margin-top: 0.7rem;
            }
        }
    </style>
@endpush