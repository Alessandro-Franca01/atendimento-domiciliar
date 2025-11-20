<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('therapy_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('patients')->onDelete('cascade');
            $table->foreignId('professional_id')->constrained('professionals')->onDelete('cascade');
            $table->string('descricao');
            $table->integer('total_sessoes');
            $table->decimal('valor_por_sessao', 10, 2)->nullable();
            $table->decimal('desconto_percentual', 5, 2)->default(0);
            $table->decimal('desconto_valor', 10, 2)->nullable();
            $table->integer('sessoes_realizadas')->default(0);
            $table->date('data_inicio');
            $table->date('data_fim_prevista')->nullable();
            $table->decimal('valor_total', 10, 2)->nullable();
            $table->decimal('valor_pago', 10, 2)->default(0);
            $table->enum('status', ['ativo', 'concluido', 'suspenso'])->default('ativo');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('therapy_sessions');
    }
};