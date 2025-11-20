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
        if (!Schema::hasTable('therapy_sessions')) {
            return;
        }

        // drop possible FK constraints (names may differ)
        try {
            DB::statement('ALTER TABLE therapy_sessions DROP FOREIGN KEY therapy_sessions_paciente_id_foreign');
        } catch (\Throwable $e) {}

        try {
            DB::statement('ALTER TABLE therapy_sessions DROP FOREIGN KEY therapy_sessions_profissional_id_foreign');
        } catch (\Throwable $e) {}

        // Rename columns
        DB::statement('ALTER TABLE therapy_sessions CHANGE paciente_id patient_id BIGINT UNSIGNED NOT NULL');
        DB::statement('ALTER TABLE therapy_sessions CHANGE profissional_id professional_id BIGINT UNSIGNED NOT NULL');

        // Recreate foreign keys
        try {
            DB::statement('ALTER TABLE therapy_sessions ADD CONSTRAINT therapy_sessions_patient_id_foreign FOREIGN KEY (patient_id) REFERENCES patients(id) ON DELETE CASCADE');
        } catch (\Throwable $e) {}

        try {
            DB::statement('ALTER TABLE therapy_sessions ADD CONSTRAINT therapy_sessions_professional_id_foreign FOREIGN KEY (professional_id) REFERENCES professionals(id) ON DELETE CASCADE');
        } catch (\Throwable $e) {}
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('therapy_sessions')) {
            return;
        }

        try {
            DB::statement('ALTER TABLE therapy_sessions DROP FOREIGN KEY therapy_sessions_patient_id_foreign');
        } catch (\Throwable $e) {}

        try {
            DB::statement('ALTER TABLE therapy_sessions DROP FOREIGN KEY therapy_sessions_professional_id_foreign');
        } catch (\Throwable $e) {}

        DB::statement('ALTER TABLE therapy_sessions CHANGE patient_id paciente_id BIGINT UNSIGNED NOT NULL');
        DB::statement('ALTER TABLE therapy_sessions CHANGE professional_id profissional_id BIGINT UNSIGNED NOT NULL');

        try {
            DB::statement('ALTER TABLE therapy_sessions ADD CONSTRAINT therapy_sessions_paciente_id_foreign FOREIGN KEY (paciente_id) REFERENCES pacientes(id) ON DELETE CASCADE');
        } catch (\Throwable $e) {}

        try {
            DB::statement('ALTER TABLE therapy_sessions ADD CONSTRAINT therapy_sessions_profissional_id_foreign FOREIGN KEY (profissional_id) REFERENCES profissionals(id) ON DELETE CASCADE');
        } catch (\Throwable $e) {}
    }
};
