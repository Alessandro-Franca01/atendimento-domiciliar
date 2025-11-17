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
        // Rename tables from Portuguese to English (only rename sessao_horarios as others are already renamed)
        if (Schema::hasTable('sessao_horarios') && !Schema::hasTable('session_schedules')) {
            Schema::rename('sessao_horarios', 'session_schedules');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverse the renaming (back to Portuguese) - only rename session_schedules back
        if (Schema::hasTable('session_schedules') && !Schema::hasTable('sessao_horarios')) {
            Schema::rename('session_schedules', 'sessao_horarios');
        }
    }
};