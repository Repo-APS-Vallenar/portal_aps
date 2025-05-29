<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->foreignId('category_id')->constrained('ticket_categories');
            $table->foreignId('status_id')->constrained('ticket_statuses');
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('assigned_to')->nullable()->constrained('users');
            $table->enum('priority', ['baja', 'media', 'alta', 'urgente'])->default('media');
            $table->dateTime('due_date')->nullable();
            
            // Campos específicos para solicitudes de tickets
            $table->string('marca')->nullable();
            $table->string('modelo')->nullable();
            $table->string('numero_serie')->nullable();
            $table->string('ubicacion')->nullable();
            $table->string('usuario')->nullable();
            $table->string('ip_red_wifi')->nullable();
            $table->string('cpu')->nullable();
            $table->string('ram')->nullable();
            $table->string('capacidad_almacenamiento')->nullable();
            $table->string('tarjeta_video')->nullable();
            $table->string('id_anydesk')->nullable();
            $table->string('pass_anydesk')->nullable();
            $table->string('version_windows')->nullable();
            $table->string('licencia_windows')->nullable();
            $table->string('version_office')->nullable();
            $table->string('licencia_office')->nullable();
            $table->string('password_cuenta')->nullable();
            $table->date('fecha_instalacion')->nullable();
            $table->text('comentarios')->nullable();
            $table->text('solucion_aplicada')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
