@extends('layouts.app')

@section('title', 'Panel De Inicio')

@section('content')
    <!-- Contenido principal con padding-top para compensar el navbar fijo -->
    <div class="container" style="padding-top: 20px;">
        <!-- Título del Portal con animación -->
        <div class="text-center mb-4 fade-in">
            <h2 class="mb-3">Portal APS Vallenar</h2>
            <p class="lead mt-3">Acceso centralizado a todos los sistemas utilizados por los usuarios de la institución</p>
        </div>

        <!-- Carrusel Informativo -->
        <div class="row mb-4">
            <div class="col-12">
                <div id="infoCarousel" class="carousel slide shadow-lg rounded-4 overflow-hidden text-center"
                    data-bs-ride="carousel">
                    <div class="carousel-indicators">
                        <button type="button" data-bs-target="#infoCarousel" data-bs-slide-to="0" class="active"></button>
                        <button type="button" data-bs-target="#infoCarousel" data-bs-slide-to="1"></button>
                        <button type="button" data-bs-target="#infoCarousel" data-bs-slide-to="2"></button>
                    </div>
                    <div class="carousel-inner rounded">
                        <div class="carousel-item active">
                            <div
                                class="carousel-content bg-primary text-white p-5 rounded-3 shadow-lg animate__animated animate__fadeInRight">
                                <h3><i class="fas fa-clock me-2 fa-bounce"></i>Horarios de Atención</h3>
                                <p>Lunes a Viernes: 8:00 - 17:00<br>Sábados: 9:00 - 13:00</p>
                            </div>
                        </div>
                        <div class="carousel-item">
                            <div
                                class="carousel-content bg-info text-white p-5 rounded-3 shadow-lg animate__animated animate__fadeInUp">
                                <h3><i class="fas fa-phone-alt me-2 fa-bounce"></i>Contactos Importantes</h3>
                                <p>Mesa de Ayuda: +56 9 1234 5678<br>Soporte Técnico: soporte@aps-vallenar.cl</p>
                            </div>
                        </div>
                        <div class="carousel-item">
                            <div
                                class="carousel-content bg-success text-white p-5 rounded-3 shadow-lg animate__animated animate__zoomIn">
                                <h3><i class="fas fa-ticket-alt me-2 fa-bounce"></i>Plataforma de Tickets | TicketGo</h3>
                                <p>Accede a la plataforma de tickets para reportar y resolver incidencias</p>
                            </div>
                        </div>
                    </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#infoCarousel" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon"></span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#infoCarousel" data-bs-slide="next">
                        <span class="carousel-control-next-icon"></span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Sección de búsqueda mejorada -->
        <div class="row mb-4">
            <div class="col-md-6 mx-auto">
                <div class="search-container">
                    <div class="search-wrapper">
                        <i class="fas fa-search search-icon"></i>
                        <input type="text" class="search-input" id="searchPlatform" placeholder="Buscar plataforma...">
                    </div>
                </div>
            </div>
        </div>

        <!-- Plataformas -->
        <div class="section-title mb-4">
            <h3><i class="fas fa-th-large me-2"></i>Plataformas</h3>
            <div class="title-underline"></div>
        </div>

        <div class="row mb-4">
            @foreach($platforms['clinicos'] as $platform)
                <div class="col-md-3 mb-4">
                    <div class="card h-100 platform-card">
                        <div class="platform-image-container mt-4">
                                    <img src="{{ asset('images/' . ($platform['imagen'] ?? 'default-platform.png')) }}"
                                        class="platform-image lazy" loading="lazy"
                                        data-src="{{ asset('images/' . ($platform['imagen'] ?? 'default-platform.png')) }}"
                                        alt="{{ $platform['nombre'] }}">
                                </div>
                        <div class="card-body d-flex flex-column align-items-center w-100">
                            <h5 class="card-title text-center">
                                <i class="fas {{ $platform['icono'] ?? 'fa-external-link-alt' }} me-2"></i>
                                            {{ $platform['nombre'] }}
                                        </h5>
                            <p class="card-text text-center">{{ $platform['descripcion'] }}</p>
                            <a href="{{ $platform['url'] }}" class="btn btn-primary platform-button mt-auto" target="_blank"
                                            rel="noopener noreferrer">
                                <i class="fas {{ $platform['url'] != '/tickets' ? 'fa-external-link-alt' : 'fa-play' }} me-1"></i> Acceder
                                        </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    @push('styles')
        <style>
            .platform-image-container {
                width: 120px;
                height: 120px;
                display: flex;
                align-items: center;
                justify-content: center;
                background-color: #fff;
                border-radius: 10px;
                padding: 0;
                overflow: hidden;
                margin: 0 auto 10px auto;
            }

            .platform-image {
                width: 80px;
                height: 80px;
                object-fit: contain;
                transition: transform 0.3s ease;
            }

            .platform-card:hover .platform-image {
                transform: scale(1.05);
            }

            .platform-button {
                background-color: #01a3d5 !important;
                border-color: #01a3d5 !important;
                color: white !important;
                border-radius: 50px;
                padding: 8px 20px;
                transition: all 0.3s ease;
            }

            .platform-button:hover {
                background-color: #0188b3 !important;
                border-color: #0188b3 !important;
                box-shadow: 0 4px 8px rgba(1, 163, 213, 0.3) !important;
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            // Función para normalizar texto (eliminar acentos)
            function normalizarTexto(texto) {
                return texto.normalize("NFD")
                    .replace(/[\u0300-\u036f]/g, "")
                    .toLowerCase();
            }

            document.getElementById('searchPlatform').addEventListener('input', function (e) {
                const searchText = normalizarTexto(e.target.value);
                const cards = document.querySelectorAll('.platform-card');

                cards.forEach(card => {
                    const title = normalizarTexto(card.querySelector('.card-title').textContent);
                    const description = normalizarTexto(card.querySelector('.card-text').textContent);

                    if (title.includes(searchText) || description.includes(searchText)) {
                        card.closest('.col-md-3').style.display = '';
                    } else {
                        card.closest('.col-md-3').style.display = 'none';
                    }
                });
            });

            document.addEventListener('DOMContentLoaded', function () {
                // Lazy Loading para imágenes
                const lazyImages = document.querySelectorAll('img.lazy');

                const imageObserver = new IntersectionObserver((entries, observer) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            const img = entry.target;
                            img.src = img.dataset.src;
                            img.classList.add('loaded');
                            observer.unobserve(img);
                        }
                    });
                });

                lazyImages.forEach(img => imageObserver.observe(img));

                // Inicialización del carrusel
                const carousel = new bootstrap.Carousel(document.getElementById('infoCarousel'), {
                    interval: 5000,
                    touch: true
                });
            });
        </script>
    @endpush
@endsection