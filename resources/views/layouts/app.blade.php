<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'INTRANET APS')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Styles -->
   
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <style>
    html, body {
        height: 100%;
        margin: 0;
        padding: 0;
    }
    body {
        min-height: 100vh;
        display: flex;
        flex-direction: column;
    }
    main {
        flex: 1 0 auto;
    }
    .footer-institucional {
        flex-shrink: 0;
    }
    .container {
        flex: 1 0 auto;
        padding-bottom: 0;
    }
    /* Estilos del Navbar */
    .navbar {
        transition: all 0.3s ease;
        background-color: rgba(255, 255, 255, 0.95) !important;
        backdrop-filter: blur(10px);
        min-height: 50px !important;
        padding: 0 !important;
        margin-top: 0 !important;
    }

    .navbar .container-fluid {
        height: 50px;
    }

    .navbar-brand {
        padding: 0 !important;
        margin: 0 !important;
    }

    .navbar-brand img {
        transition: transform 0.3s ease;
        height: 35px !important;
        width: auto;
    }

    .navbar-brand:hover img {
        transform: scale(1.05);
    }

    .nav-link {
        position: relative;
        color: #2c3e50 !important;
        font-weight: 500 !important;
        padding: 0.25rem 1rem !important;
        transition: color 0.3s ease;
        height: 50px;
        display: flex;
        align-items: center;
    }

    .nav-link::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 50%;
        width: 0;
        height: 2px;
        background-color: #28a745;
        transition: all 0.3s ease;
        transform: translateX(-50%);
    }

    .nav-link:hover::after,
    .nav-link.active::after {
        width: 80%;
    }

    .nav-link:hover {
        color: #28a745 !important;
    }

    .navbar-toggler {
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
        margin-right: 0.5rem;
    }

    .dropdown-menu {
        border: none;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        border-radius: 10px;
    }
 
    .dropdown-item {
        padding: 0.7rem 1.5rem;
        transition: all 0.3s ease;
        font-weight: 500;
    }

    .dropdown-item:hover {
        background-color: #f8f9fa;
        color: #28a745;
        transform: translateX(5px);
    }

    .bell-floating {
        position: fixed;
        top: 55px;
        left: 40px;
        z-index: 99999;
    }
    @media (max-width: 1200px) {
        .bell-floating {
            left: 24px;
        }
    }
    @media (max-width: 768px) {
        .bell-floating {
            left: 12px;
            top: 24px;
        }
    }

    /* Ajuste para evitar que .container afecte al navbar */
    body > .container {
        flex: 1 0 auto;
        padding-bottom: 0;
    }
    </style>

    @stack('styles')
    
</head>
<body>
    @include('layouts.navbar')

    <main class="py-3" style="margin-top: 50px;">
        @yield('content')
    </main>

    <!-- Campana de notificaciones flotante visible para todos los usuarios autenticados (prueba) -->
    @auth
        <x-notification-bell />
    @endauth

    <!-- Modal de confirmación de sesión -->
    <div class="modal fade" id="sessionConfirmModal" tabindex="-1" aria-labelledby="sessionConfirmModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header bg-warning-subtle">
            <h5 class="modal-title" id="sessionConfirmModalLabel">¿Deseas continuar con tu sesión?</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
          </div>
          <div class="modal-body">
            Tu sesión está a punto de expirar por inactividad.<br>
            Si deseas seguir trabajando, haz clic en <b>Continuar</b>.<br>
            Si no, puedes cerrar sesión de forma segura.
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" id="logoutSessionBtn">Cerrar sesión</button>
            <button type="button" class="btn btn-success" id="continueSessionBtn" data-bs-dismiss="modal">Continuar</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Toasts globales -->
    <div class="position-fixed top-0 end-0 p-3" style="z-index: 1200">
        <div id="globalToast" class="toast align-items-center border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body" id="globalToastBody"></div>
                <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast" aria-label="Cerrar"></button>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @vite('resources/js/app.js')
    
    

    @include('layouts.footer')

    <script>
    // Configuración: minutos antes de expirar para mostrar el modal
    const SESSION_LIFETIME_MINUTES = {{ config('session.lifetime', 120) }};
    const WARNING_BEFORE_MINUTES = 2; // Mostrar modal 2 minutos antes de expirar
    let sessionTimeout, warningTimeout;

    function resetSessionTimers() {
        clearTimeout(sessionTimeout);
        clearTimeout(warningTimeout);
        // Tiempo hasta mostrar advertencia (en ms)
        const warningMs = (SESSION_LIFETIME_MINUTES - WARNING_BEFORE_MINUTES) * 60 * 1000;
        // Tiempo hasta expirar (en ms)
        const expireMs = SESSION_LIFETIME_MINUTES * 60 * 1000;
        warningTimeout = setTimeout(showSessionWarningModal, warningMs);
        sessionTimeout = setTimeout(autoLogout, expireMs);
    }

    function showSessionWarningModal() {
        const modal = new bootstrap.Modal(document.getElementById('sessionConfirmModal'));
        modal.show();
    }

    function autoLogout() {
        window.location.href = '{{ route('logout') }}';
    }

    document.getElementById('continueSessionBtn').addEventListener('click', function() {
        // Hacer ping al backend para refrescar la sesión
        fetch(window.location.href, { method: 'GET', credentials: 'same-origin' })
            .then(() => {
                resetSessionTimers();
            });
    });
    document.getElementById('logoutSessionBtn').addEventListener('click', function() {
        autoLogout();
    });

    // Reiniciar temporizadores en cualquier interacción
    ['click', 'keydown', 'mousemove', 'scroll'].forEach(evt => {
        document.addEventListener(evt, resetSessionTimers);
    });
    // Inicializar al cargar
    resetSessionTimers();
    </script>

    @push('scripts')
    <script>
    window.showGlobalToast = function(message, type = 'success', delay = 4000) {
        const toastEl = document.getElementById('globalToast');
        const toastBody = document.getElementById('globalToastBody');
        toastBody.innerHTML = message;
        toastEl.className = 'toast align-items-center border-0 text-bg-' + (type === 'error' ? 'danger' : type);
        const toast = new bootstrap.Toast(toastEl, { delay });
        toast.show();
    }
    </script>
    @endpush

    @stack('scripts')
    
</body>
</html> 