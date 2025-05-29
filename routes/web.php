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

// Autenticación
Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('login', [LoginController::class, 'login']);
Route::post('logout', [LoginController::class, 'logout'])->name('logout');

// Públicas
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/platforms', [PlatformController::class, 'index'])->name('platforms.index');
Route::get('/contacto', [ContactController::class, 'index'])->name('contacto');

// Rutas protegidas
Route::middleware(['auth'])->group(function () {
    // Tickets
    Route::resource('tickets', TicketController::class);
    Route::post('tickets/{ticket}/comments', [TicketController::class, 'addComment'])->name('tickets.addComment');
    Route::get('tickets/{ticket}/comments', [TicketController::class, 'getComments'])->name('tickets.getComments');
    Route::post('/tickets/{ticket}/notify', [NotificationController::class, 'notifyTicketUser'])->name('tickets.notifyUser');
    Route::delete('/tickets/{ticket}/comments/{comment}', [TicketController::class, 'deleteComment'])->name('tickets.deleteComment');

    // Rutas para documentos de tickets (solo DELETE y GET protegidas)
    Route::delete('/tickets/documents/{document}', [TicketDocumentController::class, 'destroy'])->name('tickets.documents.destroy');
    Route::get('/tickets/documents/{document}/download', [TicketDocumentController::class, 'download'])->name('tickets.documents.download');

    // Comentarios
    Route::get('/comments/{comment}/edit', [TicketCommentController::class, 'edit'])->name('comments.edit');
    Route::put('/comments/{comment}', [TicketCommentController::class, 'update'])->name('comments.update');
    Route::delete('/comments/{comment}', [TicketCommentController::class, 'destroy'])->name('comments.destroy');

    // Usuarios
    Route::get('/admin/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/admin/users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('/admin/users', [UserController::class, 'store'])->name('users.store');
    Route::patch('/admin/users/{user}/toggle', [UserController::class, 'toggle'])->name('users.toggle');
    Route::put('/admin/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::put('/admin/users/{user}/password', [UserController::class, 'updatePassword'])->name('users.updatePassword');

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

    // Notificaciones
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{notification}/mark-read', [NotificationController::class, 'markAsRead'])->name('notifications.mark-read');
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
    Route::delete('/notifications/{notification}', [NotificationController::class, 'destroy'])->name('notifications.destroy');
    Route::post('/notifications/cleanup', [NotificationController::class, 'cleanup'])->name('notifications.cleanup');

    // Perfil
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
});

// Ruta de subida de documentos SIN middleware auth para prueba
Route::post('/tickets/{ticket}/documents', [TicketDocumentController::class, 'store'])->name('tickets.documents.store');

// Desarrollo
Route::middleware(['auth'])->group(function () {
    Route::get('/verificar-usuario', function () {
        if (!app()->environment('local')) {
            abort(404);
        }
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
});

// Ruta para refrescar el token CSRF
Route::get('/refresh-csrf', function () {
    return response()->json(['csrfToken' => csrf_token()]);
});

require __DIR__.'/channels.php';
