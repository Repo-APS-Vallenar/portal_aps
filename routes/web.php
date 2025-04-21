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
use App\Http\Controllers\AuditController;
use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\ExportController;
// Rutas de autenticación
Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('login', [LoginController::class, 'login']);
Route::post('logout', [LoginController::class, 'logout'])->name('logout');

// Ruta principal
Route::get('/', [HomeController::class, 'index'])->name('home');

// Rutas de tickets
Route::middleware(['auth'])->group(function () {
    Route::resource('tickets', TicketController::class);
    Route::post('tickets/{ticket}/comments', [TicketController::class, 'addComment'])->name('tickets.addComment');

    // Rutas de usuarios (solo admin)
    Route::get('users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('users', [UserController::class, 'store'])->name('users.store');
});

Route::get('/platforms', [PlatformController::class, 'index'])->name('platforms.index');
Route::get('/contacto', [ContactController::class, 'index'])->name('contacto');

Route::get('/run-admin-seeder', function () {
    Artisan::call('db:seed', [
        '--class' => 'AdminUserSeeder',
        '--force' => true,
    ]);

    return 'Seeder ejecutado correctamente';
});

Route::middleware(['auth'])->group(function () {
    Route::get('/comments/{comment}/edit', [TicketCommentController::class, 'edit'])->name('comments.edit');
    Route::put('/comments/{comment}', [TicketCommentController::class, 'update'])->name('comments.update');
    Route::delete('/comments/{comment}', [TicketCommentController::class, 'destroy'])->name('comments.destroy');
});

Route::put('/tickets/comments/{id}', [TicketController::class, 'updateComment'])->name('tickets.updateComment');

Route::delete('/tickets/{ticket}/comments/{comment}', [TicketController::class, 'deleteComment'])->name('tickets.deleteComment');

Route::get('/run-seeders', function () {
    if (auth()->check() && auth()->user()->isAdmin()) {
        Artisan::call('db:seed', [
            '--class' => 'CategorySeeder',
            '--force' => true
        ]);
        Artisan::call('db:seed', [
            '--class' => 'StatusSeeder',
            '--force' => true
        ]);
        Artisan::call('db:seed', [
            '--class' => 'LocationSeeder',
            '--force' => true
        ]);
        Artisan::call('db:seed', [
            '--class' => 'AdminUserSeeder',
            '--force' => true
        ]);

        return "Seeders ejecutados correctamente.";
    }

    abort(403);
});

Route::put('/tickets/comments/{comment}', [TicketController::class, 'updateComment'])->name('tickets.updateComment');
Route::delete('/tickets/comments/{comment}', [TicketController::class, 'deleteComment'])->name('tickets.deleteComment');

Route::patch('/admin/users/{id}/toggle', [UserController::class, 'toggle'])->name('users.toggle');





Route::middleware(['auth'])->group(function () {
    Route::get('/admin/users', [UserController::class, 'index'])->name('users.index');
    Route::get('users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('users', [UserController::class, 'store'])->name('users.store');
    Route::post('/admin/users/{user}/toggle', [UserController::class, 'toggle'])->name('users.toggle');
    Route::put('/admin/users/{user}', [UserController::class, 'update'])->name('users.update');
});

Route::put('/admin/users/{user}/password', [UserController::class, 'updatePassword'])->name('users.updatePassword');

Route::middleware(['auth'])->get('/admin/audit', [AuditLogController::class, 'index'])->name('audit.index');
Route::get('/audit', [AuditLogController::class, 'index'])->name('audit.index');


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

Route::get('/audit/search', [AuditLogController::class, 'search'])->name('audit.search');


Route::get('/audit/export/excel', [AuditLogController::class, 'exportExcel'])->name('audit.export.excel');

Route::get('/export/auditlogs', [ExportController::class, 'exportAuditLogs'])->name('export.auditlogs');

Route::get('/audit/export/pdf', [AuditLogController::class, 'exportPdf'])->name('export.auditlogs.pdf');

//Route::post('/audit/export/selected', [ExportController::class, 'exportSelected'])->name('audit.export.selected');
Route::post('/audit/export-selected', [AuditLogController::class, 'exportSelected'])->name('audit.export.selected');

Route::get('usuarios/export/pdf', [UserController::class, 'exportUsersPdf'])->name('users.exports.pdf');

Route::get('/users/export/excel', [UserController::class, 'exportExcel'])->name('users.export.excel');

//Route::get('/export/users/pdf', [UserController::class, 'exportUsersPdf'])->name('users.export.pdf');
