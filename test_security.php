<?php
// Script para probar medidas de seguridad
echo "=== PRUEBA DE SEGURIDAD DEL PORTAL APS ===\n\n";

// 1. Verificar middleware registrado
echo "1. MIDDLEWARE REGISTRADO:\n";
$kernel = app(\Illuminate\Contracts\Http\Kernel::class);
$middlewareAliases = $kernel->getMiddleware();
if (isset($middlewareAliases['check.ownership'])) {
    echo "✅ CheckResourceOwnership middleware registrado\n";
} else {
    echo "❌ CheckResourceOwnership middleware NO registrado\n";
}

// 2. Verificar SecurityLogger
echo "\n2. SECURITY LOGGER:\n";
if (class_exists(\App\Services\SecurityLogger::class)) {
    echo "✅ SecurityLogger service disponible\n";
    
    // Probar logging
    try {
        \App\Services\SecurityLogger::logSecurityEvent(
            'TEST_EVENT',
            'Prueba de funcionamiento del sistema de logging',
            ['test' => true]
        );
        echo "✅ Logging de seguridad funcional\n";
    } catch (Exception $e) {
        echo "❌ Error en logging: " . $e->getMessage() . "\n";
    }
} else {
    echo "❌ SecurityLogger service NO disponible\n";
}

// 3. Verificar configuración de sesiones
echo "\n3. CONFIGURACIÓN DE SESIONES:\n";
echo "Session Lifetime: " . config('session.lifetime') . " minutos\n";
echo "Session Encrypt: " . (config('session.encrypt') ? 'Sí' : 'No') . "\n";
echo "Secure Cookies: " . (config('session.secure') ? 'Sí' : 'No') . "\n";
echo "SameSite: " . config('session.same_site') . "\n";

// 4. Verificar configuración de logging
echo "\n4. CANALES DE LOGGING:\n";
$channels = config('logging.channels');
if (isset($channels['security'])) {
    echo "✅ Canal de seguridad configurado\n";
    echo "Path: " . $channels['security']['path'] . "\n";
    echo "Retención: " . $channels['security']['days'] . " días\n";
} else {
    echo "❌ Canal de seguridad NO configurado\n";
}

// 5. Verificar archivos de log
echo "\n5. ARCHIVOS DE LOG:\n";
$securityLogPath = storage_path('logs');
if (is_dir($securityLogPath)) {
    echo "✅ Directorio de logs existe\n";
    $logFiles = glob($securityLogPath . '/security*.log');
    if (!empty($logFiles)) {
        echo "✅ Archivos de log de seguridad encontrados: " . count($logFiles) . "\n";
    } else {
        echo "⚠️ No se encontraron archivos de log de seguridad (normal si es primera ejecución)\n";
    }
} else {
    echo "❌ Directorio de logs NO existe\n";
}

// 6. Verificar middleware en rutas críticas
echo "\n6. RUTAS PROTEGIDAS:\n";
$router = app('router');
$routes = $router->getRoutes();
$protectedRoutes = 0;
$totalRoutes = 0;

foreach ($routes as $route) {
    if (str_contains($route->uri(), 'tickets/') || str_contains($route->uri(), 'notifications/')) {
        $totalRoutes++;
        $middleware = $route->middleware();
        if (in_array('auth', $middleware) || in_array('check.ownership', $middleware)) {
            $protectedRoutes++;
        }
    }
}

echo "Rutas críticas protegidas: {$protectedRoutes}/{$totalRoutes}\n";

echo "\n=== FIN DE PRUEBAS ===\n";
