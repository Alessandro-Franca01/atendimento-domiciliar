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
        if (!Schema::hasTable('sessoes')) {
            return;
        }

        // Try to drop existing foreign keys if present (names may vary)
        try {
            DB::statement('ALTER TABLE sessoes DROP FOREIGN KEY sessoes_paciente_id_foreign');
        } catch (\Throwable $e) {}

        try {
            DB::statement('ALTER TABLE sessoes DROP FOREIGN KEY sessoes_profissional_id_foreign');
        } catch (\Throwable $e) {}

        // Rename columns (use explicit types to avoid doctrine/dbal dependency)
        DB::statement('ALTER TABLE sessoes CHANGE paciente_id patient_id BIGINT UNSIGNED NOT NULL');
        DB::statement('ALTER TABLE sessoes CHANGE profissional_id professional_id BIGINT UNSIGNED NOT NULL');

        // Recreate foreign keys pointing to the original tables
        try {
            DB::statement('ALTER TABLE sessoes ADD CONSTRAINT sessoes_patient_id_foreign FOREIGN KEY (patient_id) REFERENCES pacientes(id) ON DELETE CASCADE');
        } catch (\Throwable $e) {}

        try {
            DB::statement('ALTER TABLE sessoes ADD CONSTRAINT sessoes_professional_id_foreign FOREIGN KEY (professional_id) REFERENCES profissionals(id) ON DELETE CASCADE');
        } catch (\Throwable $e) {}
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('sessoes')) {
            return;
        }

        try {
            DB::statement('ALTER TABLE sessoes DROP FOREIGN KEY sessoes_patient_id_foreign');
        } catch (\Throwable $e) {}

        try {
            DB::statement('ALTER TABLE sessoes DROP FOREIGN KEY sessoes_professional_id_foreign');
        } catch (\Throwable $e) {}

        DB::statement('ALTER TABLE sessoes CHANGE patient_id paciente_id BIGINT UNSIGNED NOT NULL');
        DB::statement('ALTER TABLE sessoes CHANGE professional_id profissional_id BIGINT UNSIGNED NOT NULL');

        try {
            DB::statement('ALTER TABLE sessoes ADD CONSTRAINT sessoes_paciente_id_foreign FOREIGN KEY (paciente_id) REFERENCES pacientes(id) ON DELETE CASCADE');
        } catch (\Throwable $e) {}

        try {
            DB::statement('ALTER TABLE sessoes ADD CONSTRAINT sessoes_profissional_id_foreign FOREIGN KEY (profissional_id) REFERENCES profissionals(id) ON DELETE CASCADE');
        } catch (\Throwable $e) {}
    }
};
