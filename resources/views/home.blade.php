@extends('layouts.app')

@section('content')
@include('layouts.navbar')
<!-- Contenido principal con padding-top para compensar el navbar fijo -->
<div class="container" style="padding-top: 20px;">
    <!-- Título del Portal con animación -->
    <div class="text-center mb-4 fade-in">
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
                <span class="logo-letter"> </span>
                <span class="logo-letter"> </span>
                <span class="logo-letter"> </span>
                <span class="logo-letter">A</span>
                <span class="logo-letter">P</span>
                <span class="logo-letter">S</span>
            </div>

        </div>
        <p class="lead mt-3">Acceso centralizado a todos los sistemas utilizados por los usuarios de la institución</p>
    </div>

    <!-- Carrusel Informativo -->
    <div class="row mb-4">
        <div class="col-12">
            <div id="infoCarousel" class="carousel slide shadow-sm rounded" data-bs-ride="carousel">
                <div class="carousel-indicators">
                    <button type="button" data-bs-target="#infoCarousel" data-bs-slide-to="0" class="active"></button>
                    <button type="button" data-bs-target="#infoCarousel" data-bs-slide-to="1"></button>
                    <button type="button" data-bs-target="#infoCarousel" data-bs-slide-to="2"></button>
                </div>
                <div class="carousel-inner rounded">
                    <div class="carousel-item active">
                        <div class="carousel-content bg-primary text-white p-4">
                            <h3><i class="fas fa-clock me-2"></i>Horarios de Atención</h3>
                            <p>Lunes a Viernes: 8:00 - 17:00<br>Sábados: 9:00 - 13:00</p>
                        </div>
                    </div>
                    <div class="carousel-item">
                        <div class="carousel-content bg-info text-white p-4">
                            <h3><i class="fas fa-phone-alt me-2"></i>Contactos Importantes</h3>
                            <p>Mesa de Ayuda: +56 9 1234 5678<br>Soporte Técnico: soporte@aps-vallenar.cl</p>
                        </div>
                    </div>
                    <div class="carousel-item">
                        <div class="carousel-content bg-success text-white p-4">
                            <h3><i class="fas fa-file-alt me-2"></i>Documentación</h3>
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

    <!-- Botón destacado para tickets con animación 
    @guest
    <div class="row mb-4">
        <div class="col-12 text-center">
            <a href="{{ route('login') }}" class="btn btn-lg btn-success pulse-button">
                <i class="fas fa-ticket-alt me-2"></i>
                Plataforma de Tickets
            </a>
        </div>
    </div>
    @endguest-->

    <!-- Plataformas Generalizadas con título mejorado -->
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
                                 class="platform-image lazy"
                                 loading="lazy"
                                 data-src="{{ asset('images/' . ($platform['imagen'] ?? 'default-platform.png')) }}"
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
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Pie de página con información institucional -->
    <footer class="footer">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-3 text-center">
                    <img src="{{ asset('images/municipalidad-vallenar.png') }}" 
                        alt="Municipalidad de Vallenar" 
                        class="footer-logo">
                </div>
                <div class="col-md-6 text-center">
                    <p><i class="fas fa-map-marker-alt"></i> Calle Marañon #1379</p>
                    <p>Portal APS Vallenar &copy; {{ date('Y') }}</p>
                </div>
                <div class="col-md-3 text-center">
                    <img src="{{ asset('images/departamento-salud.png') }}" 
                        alt="Departamento de Salud" 
                        class="footer-logo">
                </div>
            </div>
        </div>
    </footer>
</div>

@push('styles')
<style>
/* Estilos del Navbar */
.navbar {
    transition: all 0.3s ease;
    background-color: rgba(255, 255, 255, 0.95) !important;
    backdrop-filter: blur(10px);
}

.navbar-brand img {
    transition: transform 0.3s ease;
}

.navbar-brand:hover img {
    transform: scale(1.05);
}

.nav-link {
    position: relative;
    color: #2c3e50 !important;
    font-weight: 500;
    padding: 0.5rem 1rem !important;
    transition: color 0.3s ease;
}

.nav-link::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 50%;
    width: 0;
    height: 2px;
    background-color: #01a3d5;
    transition: all 0.3s ease;
    transform: translateX(-50%);
}

.nav-link:hover::after,
.nav-link.active::after {
    width: 80%;
}

.nav-link:hover {
    color: #01a3d5 !important;
}

.dropdown-menu {
    border: none;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    border-radius: 10px;
}



.dropdown-item {
    padding: 0.7rem 1.5rem;
    transition: all 0.3s ease;
}

.dropdown-item:hover {
    background-color: #f8f9fa;
    color: #28a745;
    transform: translateX(5px);
}

/* Estilos generales */
body {
  min-height: 100vh;
  margin: 0;
  display: flex;
  flex-direction: column;
}

/* Container principal */
.container {
  flex: 1 0 auto;
}

/* El contenido principal */
.content {
  background-color: #ffffff;
  margin: 1em;
  padding: 1em;
  margin-bottom: 2rem;
}

/* Estilos del footer completamente revisados */
.footer {
  position: relative;
  width: 100%;
  height: 10px;
  background-color: #f8f9fa; /* o quita este color si prefieres que sea transparente como en la imagen */
  padding: 5px 0;
  margin-top: 15px;
  border-top: 1px solid #e7e7e7; /* Añade una línea sutil como separador */
}

/* Estructura más compacta para el contenido del footer */
.footer .row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin: 0;
}

/* Ajustes para los logos */
.footer-logo {
  max-height: 200px; /* Logos más pequeños */
  max-width: 100%;
  margin: 5px 0;
}

/* Ajustes específicos para el texto */
.footer .col-md-4 p {
  margin: 0;
  font-size: 0.85rem;
  color: #333; /* Color del texto oscuro como en la imagen */
}

/* Quitar todas las clases de padding y margin que puedan estar afectando */
.footer.mt-4 {
  margin-top: 0 !important;
}

.footer.py-3 {
  padding-top: 0 !important;
  padding-bottom: 0 !important;
}

/* Asegurar que las columnas estén bien dimensionadas */
.footer .col-md-4 {
  padding: 0 10px;
}

.footer p {
  margin-bottom: 0.25rem; /* Reducimos el espacio entre párrafos */
  font-size: 0.9rem; /* Opcionalmente, texto un poco más pequeño */
}

.footer-logo:hover {
    transform: scale(1.05);
}

/* Logo CSS */
.logo-container {
    display: flex;
    flex-direction: column;
    align-items: center;
    margin-bottom: 1rem;
}

.logo-text {
    display: flex;
    gap: 0.5rem;
    margin: 0.2rem 0;
}

.logo-letter {
    font-size: 3.5rem;
    font-weight: 800;
    color: #2c3e50;
    text-transform: uppercase;
    position: relative;
    transition: all 0.3s ease;
}

.logo-text:first-child .logo-letter {
    color: #28a745;
}



.logo-text:first-child .logo-letter:nth-child(24) {
    color: #322f6c;
}

.logo-text:first-child .logo-letter:nth-child(25) {
    color: #f29307;
}
.logo-text:first-child .logo-letter:nth-child(26) {
    color: #01a3d5;
}

.logo-letter::after {
    content: '';
    position: absolute;
    bottom: -5px;
    left: 0;
    width: 100%;
    height: 3px;
    background: currentColor;
    transform: scaleX(0);
    transition: transform 0.3s ease;
}

.logo-container:hover .logo-letter::after {
    transform: scaleX(1);
}

.logo-container:hover .logo-letter {
    transform: translateY(-5px);
}

/* Animación de entrada */
.fade-in {
    animation: fadeIn 1s ease-in;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-20px); }
    to { opacity: 1; transform: translateY(0); }
}

/* Barra de búsqueda mejorada */
.search-container {
    background-color: white;
    border-radius: 15px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
    padding: 8px;
    transition: all 0.3s ease;
}

.search-container:hover {
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.12);
    transform: translateY(-2px);
}

.search-wrapper {
    position: relative;
    display: flex;
    align-items: center;
}

.search-icon {
    position: absolute;
    left: 15px;
    color: #3498db;
    font-size: 1.1rem;
    transition: all 0.3s ease;
}

.search-input {
    width: 100%;
    padding: 12px 15px 12px 45px;
    border: none;
    border-radius: 10px;
    background-color: #f8f9fa;
    font-size: 1rem;
    color: #2c3e50;
    transition: all 0.3s ease;
}

.search-input:focus {
    outline: none;
    background-color: #fff;
    box-shadow: inset 0 0 0 2px #3498db;
}

/* Botón de tickets */
.pulse-button {
    animation: pulse 2s infinite;
    box-shadow: 0 0 0 0 rgba(40, 167, 69, 0.7);
    border-radius: 50px;
    padding: 12px 30px;
}

@keyframes pulse {
    0% { box-shadow: 0 0 0 0 rgba(40, 167, 69, 0.7); }
    70% { box-shadow: 0 0 0 15px rgba(40, 167, 69, 0); }
    100% { box-shadow: 0 0 0 0 rgba(40, 167, 69, 0); }
}

/* Título de sección */
.section-title {
    text-align: center;
    position: relative;
}

.section-title h2 {
    display: inline-block;
    margin-bottom: 10px;
    color: #333;
    font-weight: 600;
}

.title-underline {
    height: 3px;
    width: 100px;
    background: linear-gradient(90deg, #007bff, #28a745);
    margin: 0 auto;
    border-radius: 3px;
}

/* Tarjetas de plataforma */
.platform-card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    border: none;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
}

.platform-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
}

.platform-image-container {
    width: 100%;
    height: 100%;
    min-height: 200px;
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: #f8f9fa;
    padding: 1rem;
    transition: background-color 0.3s ease;
}

.platform-card:hover .platform-image-container {
    background-color: #e9ecef;
}

.platform-image {
    max-width: 100%;
    max-height: 100%;
    width: auto;
    height: auto;
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

/* Botones */
.btn-primary {
    background-color: #01a3d5 !important;
    border-color: #01a3d5 !important;
    transition: all 0.3s ease;
}

.btn-primary:hover, 
.btn-primary:focus {
    background-color: #0188b3 !important;
    border-color: #0188b3 !important;
    box-shadow: 0 4px 8px rgba(1, 163, 213, 0.3) !important;
}

.btn-primary:active {
    background-color: #016e91 !important;
    border-color: #016e91 !important;
}

/* Estilos del Carrusel */
.carousel {
    border-radius: 10px;
    overflow: hidden;
}

.carousel-content {
    min-height: 200px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    background: linear-gradient(45deg, #01a3d5, #0188b3);
}

.carousel-content h3 {
    color: white;
    margin-bottom: 1rem;
}

.carousel-content p {
    font-size: 1.1rem;
    margin-bottom: 0;
}

.carousel-indicators {
    margin-bottom: 1rem;
}

.carousel-control-prev,
.carousel-control-next {
    width: 5%;
}

/* Optimización de imágenes lazy loading */
.lazy {
    opacity: 0;
    transition: opacity 0.3s ease-in;
}

.lazy.loaded {
    opacity: 1;
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

document.getElementById('searchPlatform').addEventListener('input', function(e) {
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

document.addEventListener('DOMContentLoaded', function() {
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