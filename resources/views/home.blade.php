@extends('layouts.app')

@section('content')
    @include('layouts.navbar')
    <!-- Contenido principal con padding-top para compensar el navbar fijo -->
    <div class="container" style="padding-top: 20px;">
        <!-- Título del Portal con animación -->
        <div class="text-center mb-4 fade-in">
            <p class="lead mt-3">Acceso centralizado a todos los sistemas utilizados por los usuarios de la institución
            </p>
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
                                <h3><i class="fas fa-file-alt me-2 fa-bounce"></i>Documentación</h3>
                                <p>Encuentra manuales y guías de uso en la sección de Documentos</p>
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

        <div class="section-title mb-3">
            <h2>Plataformas Utilizadas</h2>
            <div class="title-underline"></div>
        </div>

        <div class="row mb-4">
            @foreach($platforms['clinicos'] as $platform)
                <div class="col-md-4 mb-4">
                    <div class="card h-100 platform-card">
                        <div class="row g-0">
                            <div class="col-md-4">
                                <div class="platform-image-container">
                                    <img src="{{ asset('images/' . ($platform['imagen'] ?? 'default-platform.png')) }}"
                                        class="platform-image lazy" loading="lazy"
                                        data-src="{{ asset('images/' . ($platform['imagen'] ?? 'default-platform.png')) }}"
                                        alt="{{ $platform['nombre'] }}">
                                </div>
                            </div>
                            @if($platform['url'] != '/login')
                                <div class="col-md-8">
                                    <div class="card-body">
                                        <h5 class="card-title">
                                            <i class="fas {{ $platform['icono'] }} me-2"></i>
                                            {{ $platform['nombre'] }}
                                        </h5>
                                        <p class="card-text">{{ $platform['descripcion'] }}</p>
                                        <a href="{{ $platform['url'] }}" class="btn btn-primary platform-button" target="_blank"
                                            rel="noopener noreferrer">
                                            <i class="fas fa-external-link-alt me-1"></i> Acceder
                                        </a>
                                    </div>
                                </div>
                            @else
                                <div class="col-md-8">
                                    <div class="card-body">
                                        <h5 class="card-title">
                                            <i class="fas {{ $platform['icono'] }} me-2"></i>
                                            {{ $platform['nombre'] }}
                                        </h5>
                                        <p class="card-text">{{ $platform['descripcion'] }}</p>
                                        <a href="{{ $platform['url'] }}" class="btn btn-primary platform-button"
                                            rel="noopener noreferrer">
                                            <i class="fas fa-play me-1"></i> Acceder
                                        </a>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @include('layouts.footer')



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
                        card.closest('.col-md-4').style.display = '';
                    } else {
                        card.closest('.col-md-4').style.display = 'none';
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