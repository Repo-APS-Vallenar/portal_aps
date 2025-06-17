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
            // Primero, añadir la nueva columna location_id
            $table->foreignId('location_id')->nullable()->constrained('locations')->onDelete('set null')->after('ubicacion');
            
            // Luego, eliminar la columna ubicacion
            $table->dropColumn('ubicacion');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('equipment_inventories', function (Blueprint $table) {
            // Revertir el orden: primero eliminar la clave foránea
            $table->dropConstrainedForeignId('location_id');
            
            // Luego, volver a añadir la columna ubicacion si es necesario para revertir
            $table->string('ubicacion')->nullable()->after('observaciones'); // Reemplazar con el after correcto si se conoce
        });
    }
};
