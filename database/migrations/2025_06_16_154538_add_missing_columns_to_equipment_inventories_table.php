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
        Schema::table('equipment_inventories', function (Blueprint $table) {
            $table->string('tipo')->nullable()->after('numero_serie');
            $table->string('estado')->nullable()->after('tipo');
            $table->date('fecha_adquisicion')->nullable()->after('ubicacion');
            $table->date('ultima_mantenimiento')->nullable()->after('fecha_adquisicion');
            $table->date('proximo_mantenimiento')->nullable()->after('ultima_mantenimiento');
            $table->text('observaciones')->nullable()->after('proximo_mantenimiento');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('equipment_inventories', function (Blueprint $table) {
            $table->dropColumn([
                'tipo',
                'estado',
                'fecha_adquisicion',
                'ultima_mantenimiento',
                'proximo_mantenimiento',
                'observaciones'
            ]);
        });
    }
};
