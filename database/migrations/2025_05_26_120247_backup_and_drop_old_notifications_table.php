<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Crear backup de la tabla notifications
        Schema::dropIfExists('notifications_backup');
        DB::statement('CREATE TABLE notifications_backup AS TABLE notifications');
        // Eliminar la tabla notifications antigua
        Schema::dropIfExists('notifications');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Restaurar backup si es necesario
        Schema::dropIfExists('notifications');
        DB::statement('CREATE TABLE notifications AS TABLE notifications_backup');
    }
};
