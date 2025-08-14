<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-bs-theme="light" data-theme="light">
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
    /* Variables CSS para modo oscuro - Paleta profesional */
    :root {
        /* Modo claro - Colores profesionales */
        --bg-color: #ffffff;
        --text-color: #1a202c;
        --text-secondary: #4a5568;
        --border-color: #e2e8f0;
        --card-bg: #ffffff;
        --navbar-bg: #ffffff;
        --shadow: rgba(0, 0, 0, 0.1);
        --input-bg: #ffffff;
        --input-border: #cbd5e0;
        --sidebar-bg: #f7fafc;
        --bg-primary: #ffffff;
        --bg-secondary: #f7fafc;
        --bg-tertiary: #edf2f7;
        --hover-color: #f7fafc;
        --hover-secondary: #edf2f7;
        --primary-color: #28a745;
        --accent-color: #3182ce;
        --surface-color: #ffffff;
        --divider-color: #e2e8f0;
    }

    [data-theme="dark"] {
        /* Modo oscuro - Paleta profesional */
        --bg-color: #0f172a;
        --text-color: #f1f5f9;
        --text-secondary: #cbd5e0;
        --border-color: #334155;
        --card-bg: #1e293b;
        --navbar-bg: #1e293b;
        --shadow: rgba(0, 0, 0, 0.4);
        --input-bg: #334155;
        --input-border: #475569;
        --sidebar-bg: #1e293b;
        --bg-primary: #1e293b;
        --bg-secondary: #334155;
        --bg-tertiary: #475569;
        --hover-color: #334155;
        --hover-secondary: #475569;
        --primary-color: #10b981;
        --accent-color: #3b82f6;
        --surface-color: #1e293b;
        --divider-color: #475569;
    }

    html, body {
        height: 100%;
        margin: 0;
        padding: 0;
        background-color: var(--bg-color);
        color: var(--text-color);
        transition: background-color 0.3s ease, color 0.3s ease;
    }

    /* Asegurar que el contenido principal tenga el fondo correcto */
    main, .container, .container-fluid, .row, .col {
        background-color: var(--bg-color) !important;
        color: var(--text-color) !important;
    }

    /* Asegurar que todas las páginas tengan el fondo correcto */
    .content, .main-content, .page-content {
        background-color: var(--bg-color) !important;
        color: var(--text-color) !important;
    }

    /* Asegurar que TODOS los elementos usen el color correcto */
    body, body *, .container, .container *, 
    h1, h2, h3, h4, h5, h6, p, span, div, 
    td, th, label, strong, em, small,
    .table td, .table th, .table tbody tr td,
    .dropdown-item, .nav-link, .btn,
    .form-label, .form-text, .text-muted,
    .badge, .alert, .card-body, .card-title,
    .modal-body, .modal-title, .text-secondary,
    .small, .fw-bold, .user-dropdown-header,
    .user-dropdown-item {
        color: var(--text-color) !important;
    }

    /* Forzar color específico para elementos problemáticos */
    [data-theme="dark"] .text-secondary,
    [data-theme="dark"] .text-muted,
    [data-theme="dark"] .small,
    [data-theme="dark"] .user-dropdown-header div,
    [data-theme="dark"] .dropdown-item,
    [data-theme="dark"] .table td,
    [data-theme="dark"] .table th,
    [data-theme="dark"] .user-dropdown-item,
    [data-theme="dark"] .user-dropdown-item span,
    [data-theme="dark"] .dropdown-divider {
        color: #ffffff !important;
    }

    /* Divider del dropdown en modo oscuro */
    [data-theme="dark"] .dropdown-divider {
        border-top-color: #4a5568 !important;
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
    /* Estilos del Navbar - Profesional */
    .navbar {
        background-color: var(--navbar-bg) !important;
        border-bottom: 1px solid var(--border-color);
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
        backdrop-filter: blur(20px);
        height: 60px;
        padding: 0 !important;
        margin-top: 0 !important;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    [data-theme="dark"] .navbar {
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.3);
        border-bottom-color: var(--border-color);
    }

    .navbar .nav-link {
        color: var(--text-color) !important;
        font-weight: 500;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .navbar-brand {
        color: var(--text-color) !important;
        font-weight: 600;
    }

    /* Cards y contenedores - Profesional */
    .card {
        background-color: var(--surface-color) !important;
        border: 1px solid var(--border-color) !important;
        color: var(--text-color) !important;
        border-radius: 12px !important;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06) !important;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04) !important;
    }

    [data-theme="dark"] .card {
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.3), 0 2px 4px -1px rgba(0, 0, 0, 0.2) !important;
    }

    [data-theme="dark"] .card:hover {
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.4), 0 10px 10px -5px rgba(0, 0, 0, 0.3) !important;
    }

    .card-header {
        background-color: var(--bg-secondary) !important;
        border-bottom: 1px solid var(--divider-color) !important;
        color: var(--text-color) !important;
        border-radius: 12px 12px 0 0 !important;
        font-weight: 600;
    }

    /* Formularios */
    .form-control, .form-select {
        background-color: var(--input-bg) !important;
        border-color: var(--input-border) !important;
        color: var(--text-color) !important;
    }

    .form-control:focus, .form-select:focus {
        background-color: var(--input-bg) !important;
        border-color: var(--primary-color) !important;
        color: var(--text-color) !important;
        box-shadow: 0 0 0 0.25rem rgba(40, 167, 69, 0.25);
    }

    /* Tablas */
    .table {
        color: var(--text-color) !important;
        --bs-table-bg: var(--surface-color);
    }

    .table-striped > tbody > tr:nth-of-type(odd) > td {
        background-color: var(--bg-secondary);
    }

    .table-hover tbody tr:hover {
        background-color: var(--hover-color) !important;
    }

    /* Modales */
    .modal-content {
        background-color: var(--surface-color) !important;
        color: var(--text-color) !important;
        border: 1px solid var(--border-color);
        border-radius: 12px !important;
    }

    .modal-header {
        border-bottom-color: var(--divider-color);
        background-color: var(--bg-secondary) !important;
        border-radius: 12px 12px 0 0 !important;
    }

    .modal-footer {
        border-top-color: var(--divider-color);
        background-color: var(--bg-secondary) !important;
        border-radius: 0 0 12px 12px !important;
    }

    .navbar .container-fluid {
        height: 60px;
    }

    .navbar-brand {
        padding: 0 !important;
        margin: 0 !important;
    }

    .navbar-brand img {
        transition: transform 0.3s ease;
        height: 40px !important;
        width: auto;
    }

    .navbar-brand:hover img {
        transform: scale(1.05);
    }

    .nav-link {
        position: relative;
        color: var(--text-color) !important;
        font-weight: 500 !important;
        padding: 0.5rem 1rem !important;
        transition: color 0.3s ease;
        height: 60px;
        display: flex;
        align-items: center;
    }

    .nav-link::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 50%;
        width: 0;
        height: 3px;
        background-color: var(--primary-color);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        transform: translateX(-50%);
        border-radius: 2px;
    }

    .nav-link:hover::after,
    .nav-link.active::after {
        width: 80%;
    }

    .nav-link:hover {
        color: var(--primary-color) !important;
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
        background-color: var(--hover-color);
        color: var(--primary-color);
        transform: translateX(5px);
    }

    .bell-floating {
        position: fixed;
        top: 65px;
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
            top: 30px;
        }
    }

    /* Ajuste para evitar que .container afecte al navbar */
    body > .container {
        flex: 1 0 auto;
        padding-bottom: 0;
    }

    /* Transiciones suaves para el cambio de tema */
    * {
        transition: background-color 0.3s ease, color 0.3s ease, border-color 0.3s ease;
    }

    /* Asegurar que las transiciones no afecten otros elementos importantes */
    .btn, input, select, textarea {
        transition: background-color 0.3s ease, color 0.3s ease, border-color 0.3s ease, transform 0.2s ease;
    }

    /* Dropdown menus */
    .dropdown-menu {
        background-color: var(--card-bg);
        border-color: var(--border-color);
    }

    .dropdown-item {
        color: var(--text-color);
    }

    .dropdown-item:hover {
        background-color: var(--hover-color);
        color: var(--primary-color);
    }

    /* Dropdown del usuario - Diseño profesional */
    .user-dropdown-menu {
        background-color: var(--surface-color) !important;
        border: 1px solid var(--border-color) !important;
        border-radius: 12px !important;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04) !important;
        padding: 0 !important;
        min-width: 240px !important;
    }

    .user-dropdown-header {
        background: linear-gradient(135deg, var(--bg-secondary), var(--bg-tertiary)) !important;
        color: var(--text-color) !important;
        border-radius: 12px 12px 0 0 !important;
        padding: 1rem !important;
        border-bottom: 1px solid var(--divider-color) !important;
    }

    .user-dropdown-header .fw-bold {
        color: var(--text-color) !important;
        font-size: 1.1rem !important;
        font-weight: 600 !important;
    }

    .user-dropdown-header .text-secondary {
        color: var(--text-secondary) !important;
        font-size: 0.875rem !important;
        opacity: 0.8;
    }

    .user-dropdown-item {
        color: var(--text-color) !important;
        background-color: transparent !important;
        padding: 0.75rem 1.25rem !important;
        border: none !important;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1) !important;
        font-weight: 500 !important;
        display: flex !important;
        align-items: center !important;
    }

    .user-dropdown-item:hover {
        background-color: var(--hover-color) !important;
        color: var(--accent-color) !important;
        transform: translateX(4px) !important;
    }

    .user-dropdown-item i {
        width: 1.25rem !important;
        margin-right: 0.75rem !important;
        font-size: 1rem !important;
        opacity: 0.7;
        transition: opacity 0.2s ease !important;
    }

    .user-dropdown-item:hover i {
        opacity: 1 !important;
        color: var(--accent-color) !important;
    }

    /* Específico para modo oscuro con mejor contraste */
    [data-theme="dark"] .user-dropdown-menu {
        background-color: var(--surface-color) !important;
        border-color: var(--border-color) !important;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.4), 0 10px 10px -5px rgba(0, 0, 0, 0.2) !important;
    }

    [data-theme="dark"] .user-dropdown-header {
        background: linear-gradient(135deg, var(--bg-secondary), var(--bg-tertiary)) !important;
        border-bottom-color: var(--divider-color) !important;
    }

    [data-theme="dark"] .user-dropdown-header .text-secondary {
        color: var(--text-secondary) !important;
    }

    [data-theme="dark"] .user-dropdown-item {
        color: var(--text-color) !important;
    }

    [data-theme="dark"] .user-dropdown-item:hover {
        background-color: var(--hover-color) !important;
        color: var(--accent-color) !important;
    }

    /* Divider mejorado */
    .dropdown-divider {
        border-top: 1px solid var(--divider-color) !important;
        margin: 0.5rem 0 !important;
        opacity: 1 !important;
    }

    /* Alertas */
    .alert {
        border-color: var(--border-color);
    }

    /* Badges */
    .badge {
        background-color: var(--bg-secondary);
        color: var(--text-color);
    }

    /* Override específico para Bootstrap classes en modo oscuro */
    [data-theme="dark"] .text-dark {
        color: #ffffff !important;
    }

    [data-theme="dark"] .text-black {
        color: #ffffff !important;
    }

    [data-theme="dark"] .text-body {
        color: #ffffff !important;
    }

    [data-theme="dark"] .text-body-secondary {
        color: #e9ecef !important;
    }

    [data-theme="dark"] .btn-close {
        filter: invert(1);
    }

    /* Asegurar que placeholder text también sea visible */
    [data-theme="dark"] .form-control::placeholder,
    [data-theme="dark"] .form-select::placeholder {
        color: #a0aec0 !important;
    }

    /* Actividad reciente y elementos específicos del perfil */
    .activity-item, .recent-activity, .activity-card,
    .timeline-item, .activity-content, .profile-section {
        background-color: var(--surface-color) !important;
        color: var(--text-color) !important;
        border: 1px solid var(--border-color) !important;
    }

    /* Cards de actividad reciente */
    .activity-item .card, .recent-activity .card {
        background-color: var(--surface-color) !important;
        border-color: var(--border-color) !important;
        color: var(--text-color) !important;
    }

    /* Elementos de timeline y actividad */
    .timeline-item, .activity-timeline {
        background-color: var(--surface-color) !important;
        border-left-color: var(--border-color) !important;
    }

    /* Elementos vacíos o sin contenido */
    .empty-state, .no-activity, .placeholder-content {
        background-color: var(--surface-color) !important;
        color: var(--text-secondary) !important;
        border: 1px dashed var(--border-color) !important;
    }

    /* Específico para modo oscuro */
    [data-theme="dark"] .activity-item,
    [data-theme="dark"] .recent-activity,
    [data-theme="dark"] .activity-card,
    [data-theme="dark"] .timeline-item {
        background-color: var(--surface-color) !important;
        color: var(--text-color) !important;
        border-color: var(--border-color) !important;
    }

    /* Bootstrap cards en modo oscuro */
    [data-theme="dark"] .card {
        background-color: var(--surface-color) !important;
        border-color: var(--border-color) !important;
        color: var(--text-color) !important;
    }

    [data-theme="dark"] .card-header,
    [data-theme="dark"] .card-footer {
        background-color: var(--bg-secondary) !important;
        border-color: var(--border-color) !important;
        color: var(--text-color) !important;
    }

    [data-theme="dark"] .card-body {
        background-color: var(--surface-color) !important;
        color: var(--text-color) !important;
    }

    /* List groups en modo oscuro */
    [data-theme="dark"] .list-group-item {
        background-color: var(--surface-color) !important;
        border-color: var(--border-color) !important;
        color: var(--text-color) !important;
    }

    /* Cualquier contenido blanco/vacío en modo oscuro */
    [data-theme="dark"] .bg-white {
        background-color: var(--surface-color) !important;
    }

    [data-theme="dark"] .bg-light {
        background-color: var(--bg-secondary) !important;
    }

    /* Texto que podría estar oculto */
    [data-theme="dark"] .text-dark {
        color: var(--text-color) !important;
    }

    [data-theme="dark"] .text-muted {
        color: var(--text-secondary) !important;
    }

    /* Toasts y mensajes de alerta */
    .toast {
        background-color: var(--surface-color) !important;
        border: 1px solid var(--border-color) !important;
        color: var(--text-color) !important;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15) !important;
    }

    .toast-body {
        color: var(--text-color) !important;
    }

    /* Bootstrap toast backgrounds específicos - Solo para modo oscuro */
    [data-theme="dark"] .text-bg-success {
        background-color: #10b981 !important;
        color: #ffffff !important;
        border-color: #059669 !important;
    }

    [data-theme="dark"] .text-bg-success .toast-body {
        color: #ffffff !important;
    }

    [data-theme="dark"] .text-bg-danger {
        background-color: #ef4444 !important;
        color: #ffffff !important;
        border-color: #dc2626 !important;
    }

    [data-theme="dark"] .text-bg-danger .toast-body {
        color: #ffffff !important;
    }

    [data-theme="dark"] .text-bg-warning {
        background-color: #f59e0b !important;
        color: #ffffff !important;
        border-color: #d97706 !important;
    }

    [data-theme="dark"] .text-bg-warning .toast-body {
        color: #ffffff !important;
    }

    [data-theme="dark"] .text-bg-info {
        background-color: #3b82f6 !important;
        color: #ffffff !important;
        border-color: #2563eb !important;
    }

    [data-theme="dark"] .text-bg-info .toast-body {
        color: #ffffff !important;
    }

    /* En modo claro, usar colores estándar de Bootstrap */
    .text-bg-success .toast-body {
        color: inherit !important;
    }

    .text-bg-danger .toast-body {
        color: inherit !important;
    }

    .text-bg-warning .toast-body {
        color: inherit !important;
    }

    .text-bg-info .toast-body {
        color: inherit !important;
    }

    /* Alertas de Bootstrap - Solo específicas para modo oscuro */
    [data-theme="dark"] .alert {
        background-color: var(--surface-color) !important;
        border-color: var(--border-color) !important;
        color: var(--text-color) !important;
    }

    [data-theme="dark"] .alert-success {
        background-color: rgba(16, 185, 129, 0.2) !important;
        border-color: #10b981 !important;
        color: #10b981 !important;
    }

    [data-theme="dark"] .alert-danger {
        background-color: rgba(239, 68, 68, 0.2) !important;
        border-color: #ef4444 !important;
        color: #ef4444 !important;
    }

    [data-theme="dark"] .alert-warning {
        background-color: rgba(245, 158, 11, 0.2) !important;
        border-color: #f59e0b !important;
        color: #f59e0b !important;
    }

    [data-theme="dark"] .alert-info {
        background-color: rgba(59, 130, 246, 0.2) !important;
        border-color: #3b82f6 !important;
        color: #3b82f6 !important;
    }

    /* SweetAlert2 en modo oscuro */
    [data-theme="dark"] .swal2-popup {
        background-color: var(--surface-color) !important;
        color: var(--text-color) !important;
    }

    [data-theme="dark"] .swal2-title {
        color: var(--text-color) !important;
    }

    [data-theme="dark"] .swal2-content {
        color: var(--text-color) !important;
    }
    </style>

    @stack('styles')
    
</head>
<body>
    @include('layouts.navbar')

    <main class="py-3" style="margin-top: 60px;">
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
    const SESSION_LIFETIME_MINUTES = {{ config('session.lifetime', 120) ?? 120 }};
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

    // Forzar siempre tema claro
    function forceLight() {
        document.documentElement.setAttribute('data-theme', 'light');
        document.documentElement.setAttribute('data-bs-theme', 'light');
        document.body.setAttribute('data-bs-theme', 'light');
        document.body.setAttribute('data-theme', 'light');
        // Remover cualquier preferencia guardada
        localStorage.removeItem('theme');
    }
    
    // Aplicar tema claro cuando el DOM esté listo
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', forceLight);
    } else {
        forceLight();
    }
    
    // Forzar tema claro en cada carga de página
    window.addEventListener('load', forceLight);
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