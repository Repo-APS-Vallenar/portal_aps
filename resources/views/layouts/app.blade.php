<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Portal APS') }}</title>
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


    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @vite('resources/js/app.js')
    @stack('scripts')
    @yield('scripts')

    @include('layouts.footer')
</body>
</html> 