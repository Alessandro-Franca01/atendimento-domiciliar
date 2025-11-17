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
        Schema::create('sessoes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('paciente_id')->constrained('pacientes');
            $table->foreignId('profissional_id')->constrained('profissionals');
            $table->string('descricao');
            $table->integer('total_sessoes');
            $table->integer('sessoes_realizadas')->default(0);
            $table->date('data_inicio');
            $table->date('data_fim_prevista')->nullable();
            $table->enum('status', ['ativo', 'concluido', 'suspenso'])->default('ativo');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sessoes');
    }
};
