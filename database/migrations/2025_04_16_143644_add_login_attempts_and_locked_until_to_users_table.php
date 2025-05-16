<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'login_attempts')) {
                $table->unsignedTinyInteger('login_attempts')->default(0);
            }
            if (!Schema::hasColumn('users', 'locked_until')) {
                $table->timestamp('locked_until')->nullable();
            }
            if (!Schema::hasColumn('users', 'last_login_attempt_at')) {
                $table->timestamp('last_login_attempt_at')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['login_attempts', 'locked_until', 'last_login_attempt_at']);
        });
    }
};
