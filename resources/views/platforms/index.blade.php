@extends('layouts.app')

@section('title', 'Plataformas')

@section('content')
    <div class="container">
        <!-- Encabezado de la página -->
        <div class="text-center mb-4 fade-in">
            <p class="lead text-muted">Encuentra aquí todas las plataformas y sistemas utilizados en la institución por el
                personal de salud</p>
        </div>

        <!-- Filtros y búsqueda mejorados -->
        <div class="search-filter-container mb-5">
            <div class="row g-3">
                <div class="col-md-8">
                    <div class="search-wrapper">
                        <div class="search-icon-wrapper">
                            <i class="fas fa-search"></i>
                        </div>
                        <input type="text" class="form-control search-input" id="searchPlatform"
                            placeholder="Buscar por nombre, descripción o categoría...">
                        <div class="search-clear" id="searchClear">
                            <i class="fas fa-times"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="d-flex justify-content-end">
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-outline-primary active" data-filter="all">
                                <i class="fas fa-th-large me-2"></i>Todas
                            </button>
                            <button type="button" class="btn btn-outline-primary" data-filter="salud">
                                <i class="fas fa-heartbeat me-2"></i>Salud
                            </button>
                            <button type="button" class="btn btn-outline-primary" data-filter="tecnicos">
                                <i class="fas fa-building me-2"></i>Soluciones TI
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Grid de plataformas -->
        <div class="row" id="platformsGrid">
            @foreach($platforms as $categoria => $plataformas)
                @foreach($plataformas as $platform)
                    <div class="col-md-6 col-lg-4 mb-4 platform-item" data-category="{{ $categoria }}">
                        <div class="card h-100 platform-card d-flex flex-column align-items-center justify-content-between">
                            <div class="platform-image-container mt-4">
                                <img src="{{ asset('images/' . $platform['imagen']) }}" class="platform-image" alt="{{ $platform['nombre'] }}">
                                    </div>
                            <div class="card-body d-flex flex-column align-items-center w-100">
                                <h5 class="card-title text-center mt-3">{{ $platform['nombre'] }}</h5>
                                <span class="badge bg-primary mb-2">{{ $platform['categoria'] }}</span>
                                <p class="card-text text-center">{{ $platform['descripcion'] }}</p>
                                <div class="platform-actions mt-auto w-100">
                                @if($platform['url'] != '/login')
                                        <a href="{{ $platform['url'] }}" class="btn btn-primary w-100 mb-2" target="_blank">
                                            <i class="fas fa-external-link-alt me-2"></i>Acceder
                                        </a>
                                        <button class="btn btn-outline-secondary w-100" type="button" data-bs-toggle="collapse"
                                            data-bs-target="#platformInfo{{ $loop->index }}" aria-expanded="false">
                                            <i class="fas fa-info-circle me-2"></i>Más Información
                                        </button>
                                @else
                                        <a href="{{ $platform['url'] }}" class="btn btn-primary w-100 mb-2">
                                            <i class="fas fa-play me-1"></i>Acceder
                                        </a>
                                        <button class="btn btn-outline-secondary w-100" type="button" data-bs-toggle="collapse"
                                            data-bs-target="#platformInfo{{ $loop->index }}" aria-expanded="false">
                                            <i class="fas fa-info-circle me-2"></i>Más Información
                                        </button>
                                    @endif
                                    </div>
                                <div class="collapse mt-3 w-100" id="platformInfo{{ $loop->index }}">
                                    <div class="card card-body bg-light">
                                        <h6 class="mb-2"><i class="fas fa-key me-2"></i>Guía de Acceso</h6>
                                        <p class="small mb-3">{{ $platform['guia_acceso'] }}</p>
                                        <h6 class="mb-2"><i class="fas fa-headset me-2"></i>Soporte</h6>
                                        <p class="small mb-0">{{ $platform['contacto_soporte'] }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            @endforeach
        </div>
    </div>

    @push('styles')
        <style>
            /* Estilos del buscador y filtros */
            .search-filter-container {
                background-color: #fff;
                border-radius: 15px;
                padding: 1.5rem;
                box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            }

            .search-wrapper {
                position: relative;
                display: flex;
                align-items: center;
            }

            .search-icon-wrapper {
                position: absolute;
                left: 15px;
                color: #6c757d;
                z-index: 2;
            }

            .search-input {
                padding-left: 45px;
                padding-right: 45px;
                height: 50px;
                border-radius: 25px;
                border: 2px solid #e9ecef;
                font-size: 1rem;
                transition: all 0.3s ease;
            }

            .search-input:focus {
                border-color: #01a3d5;
                box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.15);
            }

            .search-clear {
                position: absolute;
                right: 15px;
                color: #6c757d;
                cursor: pointer;
                opacity: 0;
                transition: opacity 0.3s ease;
            }

            .search-clear.visible {
                opacity: 1;
            }

            .btn-group .btn {
                border-radius: 50px;
                margin: 0 0.25rem;
                padding: 0.5rem 1rem;
            }

            .btn-group .btn.active {
                background-color: #28a745;
                border-color: #28a745;
            }

            /* Estilos de las tarjetas de plataforma */
            .platform-card {
                transition: transform 0.3s ease, box-shadow 0.3s ease;
                border: none;
                border-radius: 15px;
                overflow: hidden;
                box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            }

            .platform-card:hover {
                transform: translateY(-5px);
                box-shadow: 0 8px 25px #01a3d5;
            }

            .platform-image-container {
                width: 150px;
                height: 150px;
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
                width: 150px;
                height: 150px;
                object-fit: cover;
                border-radius: 10px;
                background: #fff;
                display: block;
                margin: 0;
                box-shadow: 0 2px 8px rgba(0,0,0,0.03);
            }

            @media (max-width: 600px) {
                .platform-image-container,
                .platform-image {
                    width: 90px;
                    height: 90px;
                }
            }

            .platform-card:hover .platform-image {
                transform: scale(1.05);
            }

            .platform-stats {
                background-color: #f8f9fa;
                border-radius: 10px;
                padding: 1rem;
            }

            .platform-stats p {
                font-weight: 600;
                color: #2c3e50;
            }

            .badge {
                font-size: 0.8rem;
                padding: 0.5rem 1rem;
                border-radius: 50px;
            }

            /* Animaciones */
            .fade-in {
                animation: fadeIn 1s ease-in;
            }

            @keyframes fadeIn {
                from {
                    opacity: 0;
                    transform: translateY(-20px);
                }

                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            /* Botón Acceder personalizado */
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
        </style>
    @endpush

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const searchInput = document.getElementById('searchPlatform');
                const searchClear = document.getElementById('searchClear');
                const platformItems = document.querySelectorAll('.platform-item');
                const filterButtons = document.querySelectorAll('[data-filter]');

                // Función para mostrar/ocultar el botón de limpiar búsqueda
                function toggleClearButton() {
                    if (searchInput.value.length > 0) {
                        searchClear.classList.add('visible');
                    } else {
                        searchClear.classList.remove('visible');
                    }
                }

                // Función para limpiar la búsqueda
                searchClear.addEventListener('click', function () {
                    searchInput.value = '';
                    toggleClearButton();
                    filterPlatforms();
                });

                // Función para filtrar plataformas
                function filterPlatforms() {
                    const searchText = searchInput.value.toLowerCase();
                    const activeFilter = document.querySelector('[data-filter].active').dataset.filter;

                    platformItems.forEach(item => {
                        const title = item.querySelector('.card-title').textContent.toLowerCase();
                        const description = item.querySelector('.card-text').textContent.toLowerCase();
                        const category = item.dataset.category;

                        const matchesSearch = title.includes(searchText) ||
                            description.includes(searchText);
                        const matchesFilter = activeFilter === 'all' ||
                            category === activeFilter;

                        if (matchesSearch && matchesFilter) {
                            item.style.display = '';
                        } else {
                            item.style.display = 'none';
                        }
                    });
                }

                // Evento de búsqueda
                searchInput.addEventListener('input', function () {
                    toggleClearButton();
                    filterPlatforms();
                });

                // Evento de filtros
                filterButtons.forEach(button => {
                    button.addEventListener('click', function () {
                        filterButtons.forEach(btn => btn.classList.remove('active'));
                        this.classList.add('active');
                        filterPlatforms();
                    });
                });
            });
        </script>
    @endpush
@endsection