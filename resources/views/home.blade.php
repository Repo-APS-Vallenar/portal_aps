@extends('layouts.app')

@section('content')
<div class="container">
    <!-- Título del Portal con animación -->
    <div class="text-center mb-5 fade-in">
        <div class="logo-container">
            <div class="logo-text">
                <span class="logo-letter">P</span>
                <span class="logo-letter">O</span>
                <span class="logo-letter">R</span>
                <span class="logo-letter">T</span>
                <span class="logo-letter">A</span>
                <span class="logo-letter">L</span>
            </div>
            <div class="logo-text">
                <span class="logo-letter">A</span>
                <span class="logo-letter">P</span>
                <span class="logo-letter">S</span>
            </div>
        </div>
        <p class="lead mt-3">Acceso centralizado a todos los sistemas utilizados por los usuarios de la institución</p>
    </div>

    <!-- Sección de búsqueda mejorada -->
    <div class="row mb-5">
        <div class="col-md-6 mx-auto">
            <div class="search-container">
                <div class="search-wrapper">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text" class="search-input" id="searchPlatform" placeholder="Buscar plataforma...">
                </div>
            </div>
        </div>
    </div>

    <!-- Botón destacado para tickets con animación -->
    @guest
    <div class="row mb-5">
        <div class="col-12 text-center">
            <a href="{{ route('login') }}" class="btn btn-lg btn-success pulse-button">
                <i class="fas fa-ticket-alt me-2"></i>
                Plataforma de Tickets
            </a>
        </div>
    </div>
    @endguest

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
                            <a href="{{ $platform['url'] }}" class="btn btn-primary platform-button">
                                <i class="fas fa-external-link-alt me-1"></i> Acceder
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>

@push('styles')
<style>
/* Estilos generales */
body {
    background-color: #f8f9fa;
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

.logo-text:last-child .logo-letter:nth-child(1) {
    color: #322f6c;
}

.logo-text:last-child .logo-letter:nth-child(2) {
    color: #f29307;
}

.logo-text:last-child .logo-letter:nth-child(3) {
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

/* Logo del portal */
.portal-logo {
    max-height: 200px;
    transition: transform 0.3s ease;
}

.portal-logo:hover {
    transform: scale(1.05);
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

.search-input::placeholder {
    color: #95a5a6;
}

.search-container:focus-within .search-icon {
    color: #2ecc71;
    transform: scale(1.1);
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
    border-radius: 50px;
    padding: 8px 20px;
    transition: all 0.3s ease;
}

.platform-button:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0, 123, 255, 0.3);
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
</script>
@endpush
@endsection 