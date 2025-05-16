@extends('layouts.app')

@section('content')
<div class="container" style="min-height: 70vh; display: flex; align-items: center; justify-content: center;">
    <div class="row justify-content-center w-100">
        <div class="col-md-7 col-lg-5">
            <div class="card shadow-lg border-0 rounded-4" style="background: linear-gradient(135deg, #2196f3 2%, #6dd5fa 100%);">
                <div class="card-header text-white fw-bold text-center border-0" style="background: transparent; font-size: 1.3rem; letter-spacing: 1px;">{{ __('Iniciar Sesión En TicketGo') }}</div>
                <div class="card-body p-4" style="background: rgba(255,255,255,0.97); border-radius: 0 0 1.5rem 1.5rem;">
                    <form method="POST" action="{{ route('login') }}">
                        @csrf
                        <div class="mb-3">
                            <label for="email" class="form-label">Correo Electrónico</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white"><i class="bi bi-envelope"></i></span>
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                                @error('email')
                                    <span class="invalid-feedback d-block" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Contraseña</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white"><i class="bi bi-lock"></i></span>
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">
                                @error('password')
                                    <span class="invalid-feedback d-block" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="mb-3 form-check">
                            <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                            <label class="form-check-label" for="remember">
                                {{ __('Recordarme') }}
                            </label>
                        </div>
                        <div class="d-grid gap-2 mb-2">
                            <button type="submit" class="btn text-white fw-bold" style="background: linear-gradient(90deg, #2196f3 0%, #6dd5fa 100%); border: none; font-size: 1.1rem;">
                                <i class="bi bi-box-arrow-in-right me-1"></i>{{ __('Iniciar Sesión') }}
                            </button>
                        </div>
                        @if (Route::has('password.request'))
                            <div class="text-center">
                                <a class="btn btn-link text-decoration-none" href="{{ route('password.request') }}">
                                    {{ __('¿Olvidaste tu contraseña?') }}
                                </a>
                            </div>
                        @endif
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
@endpush 