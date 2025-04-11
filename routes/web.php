<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\PlatformController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\TicketCommentController;

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

        return "Seeders ejecutados correctamente.";
    }

    abort(403);
});

Route::put('/tickets/comments/{comment}', [TicketController::class, 'updateComment'])->name('tickets.updateComment');
Route::delete('/tickets/comments/{comment}', [TicketController::class, 'deleteComment'])->name('tickets.deleteComment');

