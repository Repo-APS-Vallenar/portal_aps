<!-- resources/views/layouts/footer.blade.php -->
<footer class="footer-institucional">
    <div class="footer-container">
        <div class="footer-col footer-col-logo">

            <img src="{{ asset('images/deptosalud.png') }}" alt="Departamento de Salud de Vallenar"
                class="footer-logo-big">
            <div class="footer-contacto">
                <div><i class="fas fa-map-marker-alt"></i> Calle Marañón #1379</div>
                <div><i class="fas fa-phone"></i> +56 51 2 672300</div>
                <div><i class="fas fa-clock"></i> Lunes a Viernes: 8:00 - 17:00</div>
            </div>
        </div>
        <div class="footer-col footer-col-enlaces">
            <h5>Enlaces</h5>
            <ul>
                <li><a href="/tickets">Sistema de Tickets | TicketGo</a></li>
                <li><a href="https://deptodesalud.izytimecontrol.com/#/auto-consulta/login">IZY TIMECONTROL</a></li>
                <li><a href="https://www.rayenaps.cl/">Rayen</a></li>
                <li><a href="https://www.licencia.cl/sesiones/nueva/rol.profesional">IMED</a></li>
                <li><a href="https://vallenar.horafacil.cl/login">HoraFacil</a></li>
                <li><a href="https://vallenar.carrerafuncionaria.com/login">Carrera Funcionaria</a></li>
            </ul>
        </div>
        <div class=" footer-col-logo">
            <img src="{{ asset('images/municipalidad-vallenar.png') }}" alt="Municipalidad de Vallenar"
                class="footer-logo-big" style="max-width: 200px;">
        </div>
    </div>
    <div class="footer-bottom">
        Departamento de Salud de Vallenar &copy; {{ date('Y') }} | TBJE
    </div>
</footer>
<style>
    .footer-institucional {
        width: 100% !important;
        background: #232338 !important;
        color: #fff !important;
        padding: 0 !important;
        border-top: 1px solid #22224a !important;
        box-sizing: border-box !important;
    }
    .footer-container {
        max-width: 900px !important;
        margin: 0 auto !important;
        padding: 2rem 1rem 0 1rem !important;
        display: flex !important;
        flex-wrap: wrap !important;
        justify-content: center !important;
        align-items: flex-start !important;
        gap: 2rem !important;
    }
    .footer-col {
        flex: 1 1 260px !important;
        max-width: 300px !important;
        min-width: 220px !important;
        margin-bottom: 1.5rem !important;
        display: flex !important;
        flex-direction: column !important;
        align-items: center !important;
    }
    .footer-col-enlaces {
        align-items: flex-start !important;
    }
    .footer-col-logo {
        display: flex !important;
        flex-direction: column !important;
        align-items: center !important;
    }
    .footer-logo-big {
        max-width: 180px !important;
        margin-bottom: 1rem !important;
    }
    .footer-contacto {
        font-size: 1rem !important;
        color: #ffb84d !important;
        margin-top: 0.5rem !important;
    }
    .footer-col-enlaces h5 {
        color: #ffb84d !important;
        font-weight: bold !important;
        margin-bottom: 0.7rem !important;
        text-align: center !important;
        width: 100%;
    }
    .footer-col-enlaces ul {
        list-style: none !important;
        padding: 0 !important;
        margin: 0 !important;
        width: 100%;
    }
    .footer-col-enlaces ul li {
        margin-bottom: 0.5rem !important;
        text-align: center !important;
    }
    .footer-col-enlaces ul li a {
        color: #fff !important;
        text-decoration: none !important;
        transition: color 0.2s !important;
        display: inline-block;
        width: 100%;
    }
    .footer-col-enlaces ul li a:hover {
        color: #ffb84d !important;
    }
    .footer-bottom {
        text-align: center !important;
        padding: 1rem 0 0.7rem 0 !important;
        font-size: 1rem !important;
        color: #fff !important;
        background: none !important;
    }
    @media (max-width: 900px) {
        .footer-container {
            flex-direction: column !important;
            align-items: center !important;
            padding: 2rem 0.5rem 0 0.5rem !important;
            gap: 0 !important;
        }
        .footer-col {
            width: 100% !important;
            max-width: 400px !important;
            margin-bottom: 1.2rem !important;
        }
    }
    @media (max-width: 600px) {
        .footer-institucional {
            padding: 0 !important;
        }
        .footer-container {
            padding: 1.2rem 0.2rem 0 0.2rem !important;
            flex-direction: column !important;
            align-items: center !important;
            gap: 1.2rem !important;
        }
        .footer-col {
            width: 100% !important;
            max-width: 100% !important;
            margin-bottom: 1.2rem !important;
            align-items: center !important;
        }
        .footer-col-enlaces ul {
            padding-left: 0 !important;
        }
        .footer-col-enlaces ul li {
            text-align: center !important;
            margin-bottom: 0.7rem !important;
        }
        .footer-col-enlaces h5 {
            text-align: center !important;
        }
    }
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
        overflow-x: hidden;
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
        background-color: #f8f9fa;
        /* o quita este color si prefieres que sea transparente como en la imagen */
        padding: 5px 0;
        margin-top: 15px;
        border-top: 1px solid #e7e7e7;
        /* Añade una línea sutil como separador */
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
        max-height: 200px;
        /* Logos más pequeños */
        max-width: 100%;
        margin: 5px 0;
    }

    /* Ajustes específicos para el texto */
    .footer .col-md-4 p {
        margin: 0;
        font-size: 0.85rem;
        color: #333;
        /* Color del texto oscuro como en la imagen */
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
        margin-bottom: 0.25rem;
        /* Reducimos el espacio entre párrafos */
        font-size: 0.9rem;
        /* Opcionalmente, texto un poco más pequeño */
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




    .logo-text:first-child .logo-letter:nth-child(1) {
        color: #322f6c;
    }

    .logo-text:first-child .logo-letter:nth-child(2) {
        color: #f29307;
    }

    .logo-text:first-child .logo-letter:nth-child(3) {
        color: #01a3d5;
    }

    .logo-text:last-child .logo-letter {
        color: #28a745;
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
        from {
            opacity: 0;
            transform: translateY(-20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
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
        0% {
            box-shadow: 0 0 0 0 rgba(40, 167, 69, 0.7);
        }

        70% {
            box-shadow: 0 0 0 15px rgba(40, 167, 69, 0);
        }

        100% {
            box-shadow: 0 0 0 0 rgba(40, 167, 69, 0);
        }
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

    .carousel-content.bg-primary {
        background: linear-gradient(135deg, #007bff, #00c6ff);
    }

    .carousel-content.bg-info {
        background: linear-gradient(135deg, #00c6ff, #17a2b8);
    }

    .carousel-content.bg-success {
        background: linear-gradient(135deg, #17a2b8, #7bed9f);
    }
</style>