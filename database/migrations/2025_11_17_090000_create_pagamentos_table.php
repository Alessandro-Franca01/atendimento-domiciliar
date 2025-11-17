<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('pagamentos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('atendimento_id')->nullable()->constrained('atendimentos')->onDelete('cascade');
            $table->foreignId('sessao_id')->nullable()->constrained('sessoes')->onDelete('cascade');
            $table->foreignId('paciente_id')->constrained('pacientes')->onDelete('cascade');
            $table->foreignId('profissional_id')->constrained('profissionals')->onDelete('cascade');
            $table->foreignId('fatura_id')->nullable()->constrained('faturas')->onDelete('set null');
            $table->enum('metodo_pagamento', ['pix','dinheiro','cartao','transferencia']);
            $table->decimal('valor', 10, 2);
            $table->date('data_pagamento');
            $table->enum('status', ['pago','estornado','pendente'])->default('pago');
            $table->text('observacoes')->nullable();
            $table->timestamps();

            $table->unique(['atendimento_id', 'data_pagamento']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pagamentos');
    }
};