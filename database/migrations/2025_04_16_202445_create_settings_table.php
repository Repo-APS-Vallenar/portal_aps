<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        // Valor inicial
        DB::table('settings')->insert([
            'key' => 'maintenance_mode',
            'value' => 'off',
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }
};
