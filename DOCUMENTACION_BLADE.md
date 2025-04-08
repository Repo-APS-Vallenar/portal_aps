# Documentación de los .Blade en Laravel

## Directivas Básicas

### @extends
```php
@extends('layouts.app')
```
- **Propósito**: Indica que esta vista extiende una plantilla base
- **Uso**: Siempre va al principio del archivo
- **Ejemplo**: `@extends('layouts.app')` indica que esta vista usa el layout definido en `resources/views/layouts/app.blade.php`

### @section y @endsection
```php
@section('content')
    // Contenido aquí
@endsection
```
- **Propósito**: Define una sección de contenido que se insertará en el layout base
- **Uso**: El nombre de la sección debe coincidir con un `@yield` en el layout
- **Ejemplo**: `@section('content')` define el contenido principal que se insertará en `@yield('content')` del layout

### @push y @endpush
```php
@push('styles')
    <style>/* CSS aquí */</style>
@endpush

@push('scripts')
    <script>/* JavaScript aquí */</script>
@endpush
```
- **Propósito**: Agrega contenido a una pila (stack) definida en el layout
- **Uso**: Útil para agregar CSS o JavaScript específicos de una vista
- **Ejemplo**: `@push('styles')` agrega CSS a la pila 'styles' que se renderiza con `@stack('styles')` en el layout

## Directivas de Control de Flujo

### @if, @else, @endif
```php
@if(condición)
    // Contenido si la condición es verdadera
@else
    // Contenido si la condición es falsa
@endif
```
- **Propósito**: Control de flujo condicional
- **Uso**: Similar a if/else en PHP, pero con sintaxis más limpia

### @foreach y @endforeach
```php
@foreach($items as $item)
    // Código para cada elemento
@endforeach
```
- **Propósito**: Iterar sobre colecciones o arrays
- **Uso**: Similar a foreach en PHP, pero con sintaxis más limpia

### @for, @endfor
```php
@for($i = 0; $i < 10; $i++)
    // Código a repetir
@endfor
```
- **Propósito**: Bucles for tradicionales
- **Uso**: Similar a for en PHP, pero con sintaxis más limpia

## Directivas de Autenticación

### @auth y @endauth
```php
@auth
    // Contenido visible solo para usuarios autenticados
@endauth
```
- **Propósito**: Mostrar contenido solo a usuarios que han iniciado sesión
- **Uso**: Alternativa a `@if(auth()->check())`

### @guest y @endguest
```php
@guest
    // Contenido visible solo para usuarios no autenticados
@endguest
```
- **Propósito**: Mostrar contenido solo a usuarios que NO han iniciado sesión
- **Uso**: Alternativa a `@if(!auth()->check())`

## Directivas de PHP

### @php y @endphp
```php
@php
    $variable = 'valor';
    // Cualquier código PHP
@endphp
```
- **Propósito**: Ejecutar código PHP puro dentro de una vista Blade
- **Uso**: Cuando necesitas lógica más compleja que no se puede hacer con directivas Blade

## Funciones de Ayuda

### {{ }} (Echo)
```php
{{ $variable }}
```
- **Propósito**: Imprimir el valor de una variable con escape HTML automático
- **Uso**: `{{ $user->name }}` imprime el nombre del usuario con escape HTML

### {!! !!} (Echo sin escape)
```php
{!! $html !!}
```
- **Propósito**: Imprimir HTML sin escape (¡usar con precaución!)
- **Uso**: `{!! $formattedHtml !!}` imprime HTML sin procesar

### @csrf
```php
<form method="POST" action="/profile">
    @csrf
    <!-- Campos del formulario -->
</form>
```
- **Propósito**: Genera un campo oculto con token CSRF para formularios
- **Uso**: Obligatorio en todos los formularios POST para protección contra ataques CSRF

### asset()
```php
<img src="{{ asset('images/logo.png') }}">
```
- **Propósito**: Genera una URL para un recurso público
- **Uso**: Para enlaces a imágenes, CSS, JavaScript, etc. en la carpeta public

### route()
```php
<a href="{{ route('profile') }}">Perfil</a>
```
- **Propósito**: Genera una URL basada en el nombre de una ruta
- **Uso**: Para crear enlaces a rutas nombradas

## Ejemplos Prácticos

### Layout Base (app.blade.php)
```php
<!DOCTYPE html>
<html>
<head>
    <title>@yield('title', 'Título por defecto')</title>
    @stack('styles')
</head>
<body>
    @yield('content')
    
    @stack('scripts')
</body>
</html>
```

### Vista que Extiende el Layout
```php
@extends('layouts.app')

@section('title', 'Página de Inicio')

@section('content')
    <h1>Bienvenido</h1>
    
    @auth
        <p>Hola, {{ auth()->user()->name }}</p>
    @else
        <p>Por favor, inicia sesión</p>
    @endauth
    
    @foreach($items as $item)
        <div class="item">{{ $item->name }}</div>
    @endforeach
@endsection

@push('styles')
    <style>
        /* Estilos específicos de esta página */
    </style>
@endpush
```

## Consejos para Desarrolladores

1. **Mantén la lógica en los controladores**: Blade es para presentación, no para lógica compleja
2. **Usa componentes para código repetitivo**: Crea componentes reutilizables con `@component`
3. **Aprovecha las directivas de autenticación**: Son más legibles que condiciones PHP
4. **Utiliza @stack para recursos específicos**: Mantén tu código organizado
5. **Escapa siempre los datos de usuario**: Usa `{{ }}` en lugar de `{!! !!}` para datos de usuario 