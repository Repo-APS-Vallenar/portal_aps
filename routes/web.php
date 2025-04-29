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
use App\Http\Controllers\ExportController; // Assuming this controller is used for some exports
use App\Models\User; // Used in debug routes, keep for now but flag debug
use Illuminate\Support\Facades\Artisan; // Used in debug routes, keep for now but flag debug
use Illuminate\Support\Facades\Hash; // Used in debug routes, keep for now but flag debug
use Illuminate\Support\Facades\Auth; // Used in some debug routes, keep for now but flag debug


// Rutas de autenticación
Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('login', [LoginController::class, 'login']);
Route::post('logout', [LoginController::class, 'logout'])->name('logout');

// Ruta principal (accesible sin autenticación)
Route::get('/', [HomeController::class, 'index'])->name('home');

// Rutas accesibles sin autenticación (información general, contacto)
Route::get('/platforms', [PlatformController::class, 'index'])->name('platforms.index');
Route::get('/contacto', [ContactController::class, 'index'])->name('contacto');


// --- RUTAS QUE REQUIEREN AUTENTICACIÓN ---
Route::middleware(['auth'])->group(function () {

    // Rutas de Tickets
    Route::resource('tickets', TicketController::class);
    Route::post('tickets/{ticket}/comments', [TicketController::class, 'addComment'])->name('tickets.addComment');

    // Rutas de Comentarios de Tickets (usando TicketCommentController)
    // Eliminadas las rutas duplicadas o confusas en TicketController
    Route::get('/comments/{comment}/edit', [TicketCommentController::class, 'edit'])->name('comments.edit');
    Route::put('/comments/{comment}', [TicketCommentController::class, 'update'])->name('comments.update');
    Route::delete('/comments/{comment}', [TicketCommentController::class, 'destroy'])->name('comments.destroy');


    // Rutas de Usuarios
    // Todas movidas dentro de este grupo auth y duplicadas eliminadas
    Route::get('/admin/users', [UserController::class, 'index'])->name('users.index'); // <-- Nombre correcto de la ruta
    Route::get('/admin/users/create', [UserController::class, 'create'])->name('users.create'); // Ajustada URI a /admin/users/create para consistencia
    Route::post('/admin/users', [UserController::class, 'store'])->name('users.store'); // Ajustada URI a /admin/users para consistencia
    // Mantengo POST para toggle según tu última definición. Ajusta a PATCH si prefieres y cambia el verbo en el controlador/forms.
    Route::post('/admin/users/{user}/toggle', [UserController::class, 'toggle'])->name('users.toggle');
    Route::put('/admin/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::put('/admin/users/{user}/password', [UserController::class, 'updatePassword'])->name('users.updatePassword'); // Movida dentro del grupo auth


    // Rutas de Auditoría
    // Eliminada la ruta audit.index duplicada sin middleware. Solo /admin/audit con auth existe ahora.
    Route::get('/admin/audit', [AuditLogController::class, 'index'])->name('audit.index'); // Mantenemos esta
    Route::get('/admin/audit/search', [AuditLogController::class, 'search'])->name('audit.search'); // Movida dentro del grupo auth


    // Rutas de Exportación (Usuarios y Auditoría)
    // Todas movidas dentro de este grupo auth y duplicadas/confusas corregidas
    Route::get('/admin/users/export/pdf', [UserController::class, 'exportUsersPdf'])->name('users.exports.pdf'); // Ajustada URI para consistencia
    Route::get('/admin/users/export/excel', [UserController::class, 'exportExcel'])->name('users.export.excel'); // Ajustada URI para consistencia

    Route::get('/admin/audit/export/excel', [AuditLogController::class, 'exportExcel'])->name('audit.export.excel');
    // Hay 2 rutas llamando a exportPdf del AuditLogController y 1 llamando a ExportController::exportAuditLogs.
    // Mantengo una clara para PDF de AuditLogs desde AuditLogController.
    Route::get('/admin/audit/export/pdf', [AuditLogController::class, 'exportPdf'])->name('audit.export.pdf'); // Ajustada URI, renombrada para consistencia
    // Si Route::get('/export/auditlogs', ...) llamaba a ExportController::exportAuditLogs por alguna razón especial, consérvala y métele auth middleware. Si era redundante, elimínala. La elimino asumiendo redundancia.
    // Route::get('/export/auditlogs', [ExportController::class, 'exportAuditLogs'])->name('export.auditlogs'); // Eliminada - posible duplicidad/conflicto

    Route::post('/admin/audit/export-selected', [AuditLogController::class, 'exportSelected'])->name('audit.export.selected'); // Ajustada URI para consistencia


}); // --- FIN GRUPO AUTH ---


// --- RUTAS DE DEBUG / SEEDERS (ELIMINAR O PROTEGER ESTRICTAMENTE EN PRODUCCIÓN) ---
// !! ADVERTENCIA: Estas rutas no deben estar accesibles en entornos de producción sin estricta seguridad !!
// Ejemplo de protección: solo en entorno 'local'
// Route::environment(['local'])->group(function () {
    Route::get('/run-admin-seeder', function () {
        Artisan::call('db:seed', [
            '--class' => 'AdminUserSeeder',
            '--force' => true,
        ]);
        return 'Seeder AdminUserSeeder ejecutado correctamente';
    });

    Route::get('/verificar-usuario', function () {
        $user = User::where('email', 'admin@admin.com')->first();
        if (!$user) {
            return 'Usuario no encontrado.';
        }
        return [
            'email' => $user->email,
            'role' => $user->role,
            'is_active' => $user->is_active,
            'password_encriptada' => Hash::needsRehash($user->password) ? 'No' : 'Sí',
        ];
    });

    Route::get('/crear-superadmin', function () {
        $user = User::where('email', 'admin@admin.com')->first();
        if ($user) {
            return 'El usuario ya existe.';
        }
        User::create([
            'name' => 'Superadmin',
            'email' => 'admin@admin.com',
            'password' => Hash::make('password123'),
            'role' => 'superadmin',
            'is_active' => true,
        ]);
        return 'Superadmin creado exitosamente.';
    });

    Route::get('/run-seederr', function () {
        try {
            Artisan::call('db:seed', ['--force' => true]);
            return 'Seeder ejecutado correctamente.';
        } catch (\Throwable $e) {
            return response('Error al ejecutar el seeder: ' . $e->getMessage(), 500);
        }
    });
// }); // --- FIN RUTAS DE DEBUG ---

// NOTA: Los métodos resource show(), edit(), update(), destroy() de UserController no están definidos en el controlador que proporcionaste previamente,
// pero si los implementas más adelante, asegúrate de que tengan las protecciones de rol adecuadas dentro del controlador.

// NOTA: Las rutas de comentarios duplicadas y confusas que apuntaban a TicketController para update/delete han sido eliminadas, manteniendo las del TicketCommentController.
// Si updateComment/deleteComment en TicketController tienen lógica especial, deberás re-agregarlas con nombres y URIs distintas y con middleware auth.

// NOTA: La ruta de auditoría /audit sin middleware ha sido eliminada. Solo /admin/audit y sus subrutas con middleware auth existen ahora.