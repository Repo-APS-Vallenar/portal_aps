@extends('layouts.app')

@section('content')
<<<<<<< HEAD
    @include('layouts.navbar')
    <!-- Contenido principal con padding-top para compensar el navbar fijo -->
    <div class="container" style="padding-top: 20px;">
        <!-- Título del Portal con animación -->
        <div class="text-center mb-4 fade-in">
            <p class="lead mt-3">Acceso centralizado a todos los sistemas utilizados por los usuarios de la institución
            </p>
=======
<div class="container">
    <!-- Título del Portal con animación -->
    <div class="text-center mb-5 fade-in">
        <div class="logo-container">
            <div class="logo-text">
                <span class="logo-letter">I</span>
                <span class="logo-letter">N</span>
                <span class="logo-letter">T</span>
                <span class="logo-letter">R</span>
                <span class="logo-letter">A</span>
                <span class="logo-letter">N</span>
                <span class="logo-letter">E</span>
                <span class="logo-letter">T</span>
                <span class="logo-letter"> </span>
                <span class="logo-letter"> </span>
                <span class="logo-letter"> </span>
                <span class="logo-letter"> </span>
                <span class="logo-letter">V</span>
                <span class="logo-letter">A</span>
                <span class="logo-letter">L</span>
                <span class="logo-letter">L</span>
                <span class="logo-letter">E</span>
                <span class="logo-letter">N</span>
                <span class="logo-letter">A</span>
                <span class="logo-letter">R</span>
            </div>
            <div class="logo-text">
                <span class="logo-letter">A</span>
                <span class="logo-letter">P</span>
                <span class="logo-letter">S</span>
            </div>

>>>>>>> 8829c79 (Cambio de nombre de la plataforma a intranet vallenar aps mas footer con logos municipales)
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

<<<<<<< HEAD
        <!-- Sección de búsqueda mejorada -->
        <div class="row mb-4">
            <div class="col-md-6 mx-auto">
                <div class="search-container">
                    <div class="search-wrapper">
                        <i class="fas fa-search search-icon"></i>
                        <input type="text" class="search-input" id="searchPlatform" placeholder="Buscar plataforma...">
=======
    <!-- Botón destacado para tickets con animación 
    @guest
    <div class="row mb-5">
        <div class="col-12 text-center">
            <a href="{{ route('login') }}" class="btn btn-lg btn-success pulse-button">
                <i class="fas fa-ticket-alt me-2"></i>
                Plataforma de Tickets
            </a>
        </div>
    </div>
    @endguest-->

    <!-- Plataformas Generalizadas con título mejorado -->
    <div class="section-title mb-4">
        <h2>Plataformas Utilizadas</h2>
        <div class="title-underline"></div>
    </div>
    
    <div class="row mb-5">
        @foreach($platforms['clinicos'] as $platform)
        <div class="col-md-4 mb-4">
            <div class="card h-100 platform-card">
                <div class="row g-0">
                    <div class="col-md-4">
                        <div class="platform-image-container">
                            <img src="{{ asset('images/' . ($platform['imagen'] ?? 'default-platform.png')) }}" 
                                 class="platform-image" 
                                 alt="{{ $platform['nombre'] }}">
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="card-body">
                            <h5 class="card-title">
                                <i class="fas {{ $platform['icono'] }} me-2"></i>
                                {{ $platform['nombre'] }}
                            </h5>
                            <p class="card-text">{{ $platform['descripcion'] }}</p>
                            <a href="{{ $platform['url'] }}" class="btn btn-primary platform-button" target="_blank" rel="noopener noreferrer">
                                <i class="fas fa-external-link-alt me-1"></i> Acceder
                            </a>
                        </div>
>>>>>>> 8829c79 (Cambio de nombre de la plataforma a intranet vallenar aps mas footer con logos municipales)
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
                            @if($platform['url'] != '/tickets')
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
<<<<<<< HEAD
    @include('layouts.footer')

=======

    <!-- Pie de página con información institucional -->
    <footer class="footer mt-5 py-4">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-4 text-center">
                    <img src="{{ asset('images/municipalidad-vallenar.png') }}" alt="Municipalidad de Vallenar" class="footer-logo">
                </div>
                <div class="col-md-4 text-center">
                    <p>Portal APS Vallenar &copy; {{ date('Y') }}</p>
                </div>
                <div class="col-md-4 text-center">
                    <img src="{{ asset('images/departamento-salud.png') }}" alt="Departamento de Salud" class="footer-logo">
                </div>
            </div>
        </div>
    </footer>
</div>

@push('styles')
<style>
/* Estilos generales */
body {
    background-color: #f8f9fa;
    min-height: 100vh;
    display: flex;
    flex-direction: column;
    margin: 0;
    padding: 0;
}

.container {
    flex: 1 0 auto;
    padding-bottom: 0;
}

/* Footer */
.footer {
    background-color: #f1f1f1;
    border-top: 1px solid #e0e0e0;
    flex-shrink: 0;
    width: 100%;
    position: fixed;
    bottom: 0;
    left: 0;
    z-index: 100;
    padding: 10px 0;
}

.footer-logo {
    max-height: 100px;
    max-width: 100%;
    transition: transform 0.3s ease;
    margin: 10px 0;
}

.footer-logo:hover {
    transform: scale(1.05);
}
>>>>>>> 8829c79 (Cambio de nombre de la plataforma a intranet vallenar aps mas footer con logos municipales)


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

<<<<<<< HEAD
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
=======
.logo-text:first-child .logo-letter:nth-child(13) {
    color: #f29307;
}
.logo-text:first-child .logo-letter:nth-child(14) {
    color: #f29307;
}
.logo-text:first-child .logo-letter:nth-child(15) {
    color: #f29307;
}
.logo-text:first-child .logo-letter:nth-child(16) {
    color: #f29307;
}
.logo-text:first-child .logo-letter:nth-child(17) {
    color: #f29307;
}
.logo-text:first-child .logo-letter:nth-child(18) {
    color: #f29307;
}
.logo-text:first-child .logo-letter:nth-child(19) {
    color: #f29307;
}
.logo-text:first-child .logo-letter:nth-child(20) {
    color: #f29307;
}

.logo-text:nth-child(2) .logo-letter {
    color: #322f6c;
}

.logo-text:nth-child(2) .logo-letter:nth-child(2) {
    color: #f29307;
}
.logo-text:nth-child(2) .logo-letter:nth-child(3) {
    color: #01a3d5;
}
>>>>>>> 8829c79 (Cambio de nombre de la plataforma a intranet vallenar aps mas footer con logos municipales)

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