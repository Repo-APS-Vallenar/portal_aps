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
        Schema::create('equipment_inventories', function (Blueprint $table) {
            $table->id();
            $table->string('marca');
            $table->string('modelo');
            $table->string('numero_serie');
            $table->string('ubicacion');
            $table->string('usuario');
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
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('equipment_inventories');
    }
};
