<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Limpiar categorías duplicadas
        $categories = DB::table('ticket_categories')
            ->select('name', DB::raw('COUNT(*) as total'))
            ->groupBy('name')
            ->havingRaw('COUNT(*) > 1')
            ->get();

        foreach ($categories as $category) {
            // Obtener el primer ID (el que mantendremos)
            $keepId = DB::table('ticket_categories')
                ->where('name', $category->name)
                ->orderBy('id')
                ->value('id');

            // Obtener los IDs duplicados
            $duplicateIds = DB::table('ticket_categories')
                ->where('name', $category->name)
                ->where('id', '!=', $keepId)
                ->pluck('id');

            // Actualizar los tickets que usan las categorías duplicadas
            DB::table('tickets')
                ->whereIn('category_id', $duplicateIds)
                ->update(['category_id' => $keepId]);

            // Eliminar las categorías duplicadas
            DB::table('ticket_categories')
                ->whereIn('id', $duplicateIds)
                ->delete();
        }

        // Limpiar estados duplicados
        $statuses = DB::table('ticket_statuses')
            ->select('name', DB::raw('COUNT(*) as total'))
            ->groupBy('name')
            ->havingRaw('COUNT(*) > 1')
            ->get();

        foreach ($statuses as $status) {
            // Obtener el primer ID (el que mantendremos)
            $keepId = DB::table('ticket_statuses')
                ->where('name', $status->name)
                ->orderBy('id')
                ->value('id');

            // Obtener los IDs duplicados
            $duplicateIds = DB::table('ticket_statuses')
                ->where('name', $status->name)
                ->where('id', '!=', $keepId)
                ->pluck('id');

            // Actualizar los tickets que usan los estados duplicados
            DB::table('tickets')
                ->whereIn('status_id', $duplicateIds)
                ->update(['status_id' => $keepId]);

            // Eliminar los estados duplicados
            DB::table('ticket_statuses')
                ->whereIn('id', $duplicateIds)
                ->delete();
        }

        // Agregar restricciones únicas
        Schema::table('ticket_categories', function (Blueprint $table) {
            $table->unique('name');
        });

        Schema::table('ticket_statuses', function (Blueprint $table) {
            $table->unique('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ticket_categories', function (Blueprint $table) {
            $table->dropUnique(['name']);
        });

        Schema::table('ticket_statuses', function (Blueprint $table) {
            $table->dropUnique(['name']);
        });
    }
};
