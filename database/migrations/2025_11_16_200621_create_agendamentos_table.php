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
        Schema::create('agendamentos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sessao_id')->constrained('sessoes');
            $table->foreignId('sessao_horario_id')->nullable()->constrained('sessao_horarios');
            $table->foreignId('paciente_id')->constrained('pacientes');
            $table->foreignId('endereco_id')->constrained('enderecos');
            $table->foreignId('profissional_id')->constrained('profissionals');
            $table->dateTime('data_hora_inicio');
            $table->dateTime('data_hora_fim');
            $table->enum('status', ['agendado', 'confirmado', 'cancelado', 'concluido', 'faltou'])->default('agendado');
            $table->text('observacoes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agendamentos');
    }
};
