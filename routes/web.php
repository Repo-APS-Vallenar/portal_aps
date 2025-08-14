<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\PlatformController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\TicketCommentController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\ExportController;
use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Admin\ParameterController;
use App\Http\Controllers\TicketDocumentController;
use App\Http\Controllers\EquipmentInventoryController;

// Autenticación con rate limiting
Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('login', [LoginController::class, 'login'])->middleware('throttle:5,1'); // 5 intentos por minuto
Route::post('logout', [LoginController::class, 'logout'])->name('logout');

// Públicas
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/platforms', [PlatformController::class, 'index'])->name('platforms.index');
Route::get('/contacto', [ContactController::class, 'index'])->name('contacto');

// Rutas protegidas
Route::middleware(['auth'])->group(function () {
    // Tickets
    Route::get('tickets/export', [TicketController::class, 'export'])->name('tickets.export');
    Route::get('tickets/kanban', [TicketController::class, 'kanban'])->name('tickets.kanban');
    Route::patch('tickets/{ticket}/status', [TicketController::class, 'updateStatus'])->name('tickets.updateStatus')->middleware('check.ownership:ticket');
    Route::resource('tickets', TicketController::class);
    Route::post('tickets/{ticket}/comments', [TicketController::class, 'addComment'])->name('tickets.addComment')->middleware('check.ownership:ticket');
    Route::get('tickets/{ticket}/comments', [TicketController::class, 'getComments'])->name('tickets.getComments')->middleware('check.ownership:ticket');
    Route::post('/tickets/{ticket}/notify', [NotificationController::class, 'notifyTicketUser'])->name('tickets.notifyUser')->middleware('check.ownership:ticket');
    Route::delete('/tickets/{ticket}/comments/{comment}', [TicketController::class, 'deleteComment'])->name('tickets.deleteComment')->middleware('check.ownership:comment');

    // Rutas para documentos de tickets - PROTEGIDAS
    Route::post('/tickets/{ticket}/documents', [TicketDocumentController::class, 'store'])->name('tickets.documents.store')->middleware('check.ownership:ticket');
    Route::delete('/tickets/documents/{document}', [TicketDocumentController::class, 'destroy'])->name('tickets.documents.destroy')->middleware('check.ownership:document');
    Route::get('/tickets/documents/{document}/download', [TicketDocumentController::class, 'download'])->name('tickets.documents.download')->middleware('check.ownership:document');

    // Comentarios - PROTEGIDOS
    Route::get('/comments/{comment}/edit', [TicketCommentController::class, 'edit'])->name('comments.edit')->middleware('check.ownership:comment');
    Route::put('/comments/{comment}', [TicketCommentController::class, 'update'])->name('comments.update')->middleware('check.ownership:comment');
    Route::delete('/comments/{comment}', [TicketCommentController::class, 'destroy'])->name('comments.destroy')->middleware('check.ownership:comment');

    // Usuarios con rate limiting para operaciones críticas
    Route::get('/admin/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/admin/users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('/admin/users', [UserController::class, 'store'])->name('users.store')->middleware('throttle:5,10'); // 5 creaciones cada 10 min
    Route::patch('/admin/users/{user}/toggle', [UserController::class, 'toggle'])->name('users.toggle')->middleware('throttle:10,5'); // 10 toggles cada 5 min
    Route::put('/admin/users/{user}', [UserController::class, 'update'])->name('users.update')->middleware('throttle:10,5');
    Route::put('/admin/users/{user}/password', [UserController::class, 'updatePassword'])->name('users.updatePassword')->middleware('throttle:3,10'); // 3 cambios de contraseña cada 10 min

    // Auditoría
    Route::get('/admin/audit', [AuditLogController::class, 'index'])->name('audit.index');
    Route::get('/admin/audit/search', [AuditLogController::class, 'search'])->name('audit.search');
    Route::get('/admin/audit/profile/{userId}', [AuditLogController::class, 'showProfile'])->name('audit.profile');
    Route::get('/mi-bitacora', [AuditLogController::class, 'myProfile'])->name('audit.myprofile');

    // Exportación
    Route::get('/admin/users/export/pdf', [UserController::class, 'exportUsersPdf'])->name('users.exports.pdf');
    Route::get('/admin/users/export/excel', [UserController::class, 'exportExcel'])->name('users.export.excel');
    Route::get('/admin/audit/export/excel', [AuditLogController::class, 'exportExcel'])->name('audit.export.excel');
    Route::get('/admin/audit/export/pdf', [AuditLogController::class, 'exportPdf'])->name('audit.export.pdf');
    Route::post('/admin/audit/export-selected', [AuditLogController::class, 'exportSelected'])->name('audit.export.selected');

    // Notificaciones con rate limiting y protección de propiedad
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{id}/mark-read', [NotificationController::class, 'markAsRead'])->name('notifications.mark-read')->middleware(['throttle:10,1', 'check.ownership:notification']);
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read')->middleware('throttle:5,1');
    Route::delete('/notifications/{id}', [NotificationController::class, 'destroy'])->name('notifications.destroy')->middleware(['throttle:10,1', 'check.ownership:notification']);
    Route::post('/notifications/cleanup', [NotificationController::class, 'cleanup'])->name('notifications.cleanup')->middleware('throttle:3,5');

    // Perfil - PROTEGIDO
    Route::get('/perfil', [UserController::class, 'profile'])->name('profile');
    Route::post('/perfil', [UserController::class, 'updateProfile'])->name('profile.update');
    Route::post('/perfil/password', [UserController::class, 'updatePasswordFromProfile'])->name('profile.password');

    // Parámetros
    Route::get('/admin/parameters', [ParameterController::class, 'index'])->name('admin.parameters');
    Route::post('/admin/parameters/locations', [ParameterController::class, 'storeLocation'])->name('admin.parameters.locations.store');
    Route::put('/admin/parameters/locations/{location}', [ParameterController::class, 'updateLocation'])->name('admin.parameters.locations.update');
    Route::delete('/admin/parameters/locations/{location}', [ParameterController::class, 'destroyLocation'])->name('admin.parameters.locations.destroy');
    Route::post('/admin/parameters/categories', [ParameterController::class, 'storeCategory'])->name('admin.parameters.categories.store');
    Route::put('/admin/parameters/categories/{category}', [ParameterController::class, 'updateCategory'])->name('admin.parameters.categories.update');
    Route::delete('/admin/parameters/categories/{category}', [ParameterController::class, 'destroyCategory'])->name('admin.parameters.categories.destroy');
    Route::post('/admin/parameters/statuses', [ParameterController::class, 'storeStatus'])->name('admin.parameters.statuses.store');
    Route::put('/admin/parameters/statuses/{status}', [ParameterController::class, 'updateStatus'])->name('admin.parameters.statuses.update');
    Route::delete('/admin/parameters/statuses/{status}', [ParameterController::class, 'destroyStatus'])->name('admin.parameters.statuses.destroy');

    // Rutas del inventario de equipos
    Route::post('/equipment-inventory/check-serial-number', [EquipmentInventoryController::class, 'checkSerialNumber'])->name('equipment-inventory.check-serial-number');
    Route::get('equipment-inventory/export', [EquipmentInventoryController::class, 'export'])->name('equipment-inventory.export');
    Route::get('equipment-inventory/{equipment}/show-partial', [EquipmentInventoryController::class, 'showPartial'])->name('equipment-inventory.show-partial');
    Route::get('equipment-inventory/{equipment}/edit-partial', [EquipmentInventoryController::class, 'editPartial'])->name('equipment-inventory.edit-partial');
    Route::resource('equipment-inventory', EquipmentInventoryController::class)->except(['show']);
    Route::get('/equipment-inventory/search', [EquipmentInventoryController::class, 'search'])->name('equipment-inventory.search');
});

// Desarrollo - Solo en local con autenticación de superadmin
Route::middleware(['auth'])->group(function () {
    Route::get('/verificar-usuario', function () {
        // Triple verificación de seguridad
        if (!app()->environment('local')) {
            abort(404, 'Not found');
        }
        
        $user = Auth::user();
        if (!$user || $user->role !== 'superadmin') {
            abort(403, 'Unauthorized');
        }
        
        $adminUser = User::where('email', 'admin@admin.com')->first();
        if (!$adminUser) {
            return response()->json(['error' => 'Usuario no encontrado'], 404);
        }
        
        return response()->json([
            'email' => $adminUser->email,
            'role' => $adminUser->role,
            'is_active' => $adminUser->is_active,
            'password_hash_status' => Hash::needsRehash($adminUser->password) ? 'Needs rehash' : 'OK',
            'timestamp' => now()->toISOString()
        ]);
    })->middleware('throttle:3,10'); // 3 intentos cada 10 minutos
});

// Ruta para refrescar el token CSRF
Route::get('/refresh-csrf', function () {
    return response()->json(['csrfToken' => csrf_token()]);
});

// Ruta de prueba para headers de seguridad
Route::get('/test-headers', function () {
    $response = response()->json([
        'message' => 'Headers de seguridad aplicados',
        'timestamp' => now()->toISOString(),
        'environment' => app()->environment()
    ]);
    
    // Aplicar headers manualmente para esta prueba
    $response->header('X-Frame-Options', 'DENY');
    $response->header('X-Content-Type-Options', 'nosniff');
    $response->header('X-XSS-Protection', '1; mode=block');
    $response->header('Content-Security-Policy', "default-src 'self'; script-src 'self' 'unsafe-inline'; style-src 'self' 'unsafe-inline'; img-src 'self' data: https:; frame-ancestors 'none';");
    
    return $response;
});

// Ruta de prueba para rate limiting (SIN CSRF)
Route::post('/test-rate-limit', function () {
    return response()->json([
        'message' => 'Rate limit test',
        'timestamp' => now()->toISOString(),
        'ip' => request()->ip()
    ]);
})->middleware('throttle:3,1'); // 3 intentos por minuto

require __DIR__.'/channels.php';
