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
        Schema::create('atendimentos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agendamento_id')->constrained('agendamentos');
            $table->foreignId('profissional_id')->constrained('profissionals');
            $table->foreignId('paciente_id')->constrained('pacientes');
            $table->dateTime('data_realizacao');
            $table->text('evolucao');
            $table->text('procedimento_realizado');
            $table->text('assinatura_paciente')->nullable();
            $table->enum('status', ['concluido', 'interrompido'])->default('concluido');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('atendimentos');
    }
};
