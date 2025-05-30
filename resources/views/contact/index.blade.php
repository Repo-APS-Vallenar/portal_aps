@extends('layouts.app')

@section('title', 'Contacto')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h2 class="mb-0">Contacto</h2>
                </div>

                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h4>Información de Contacto</h4>
                            <p><i class="fas fa-map-marker-alt"></i> Calle Marañon #1379, Vallenar</p>
                            <p><i class="fas fa-phone"></i> +56 9 1234 5678</p>
                            <p><i class="fas fa-envelope"></i> soporte@aps-vallenar.cl</p>
                        </div>
                        <div class="col-md-6">
                            <h4>Horario de Atención</h4>
                            <p>Lunes a Viernes: 8:00 - 17:00</p>
                            <p>Sábados: 9:00 - 13:00</p>
                            <p>Domingos: Cerrado</p>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <h4>Envíanos un Mensaje</h4>
                            <form>
                                <div class="mb-3">
                                    <label for="name" class="form-label">Nombre</label>
                                    <input type="text" class="form-control" id="name" required>
                                </div>
                                <div class="mb-3">
                                    <label for="email" class="form-label">Correo Electrónico</label>
                                    <input type="email" class="form-control" id="email" required>
                                </div>
                                <div class="mb-3">
                                    <label for="subject" class="form-label">Asunto</label>
                                    <input type="text" class="form-control" id="subject" required>
                                </div>
                                <div class="mb-3">
                                    <label for="message" class="form-label">Mensaje</label>
                                    <textarea class="form-control" id="message" rows="5" required></textarea>
                                </div>
                                <button type="submit" class="btn btn-primary" style="background-color: #01a3d5 !important; border-color: #01a3d5 !important; transition: all 0.3s ease;">Enviar Mensaje</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
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
@endsection 