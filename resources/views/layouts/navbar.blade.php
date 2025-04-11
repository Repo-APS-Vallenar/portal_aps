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
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
            aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <!-- Left Side Of Navbar -->
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('/') ? 'active' : '' }}" href="{{ url('/') }}">Inicio</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('platforms*') ? 'active' : '' }}"
                        href="{{ route('platforms.index') }}">Plataformas</a>
                </li>
                @auth
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('tickets*') ? 'active' : '' }}"
                            href="{{ route('tickets.index') }}">Tickets</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('documentos*') ? 'active' : '' }}" href="#">Documentos</a>
                    </li>
                @endauth
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('contacto*') ? 'active' : '' }}"
                        href="{{ route('contacto') }}">Contacto</a>
                </li>
            </ul>

            <!-- Right Side Of Navbar -->
            <ul class="navbar-nav ms-auto">
                @auth
                    @if(Auth::user()->is_admin)
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('users.create') }}">Registrar Usuario</a>
                        </li>
                    @endif
                    <li class="nav-item dropdown">
                        <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button"
                            data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                            {{ Auth::user()->name }}
                        </a>

                        <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                            <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault();
                                                             document.getElementById('logout-form').submit();">
                                Cerrar Sesión
                            </a>

                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                        </div>
                    </li>
                @endauth
            </ul>
        </div>
    </div>
</nav>