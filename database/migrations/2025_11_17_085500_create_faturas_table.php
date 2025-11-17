<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('faturas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('paciente_id')->constrained('pacientes')->onDelete('cascade');
            $table->decimal('valor_total', 10, 2);
            $table->date('data_emissao');
            $table->date('data_vencimento');
            $table->enum('status', ['aberta','paga','vencida','cancelada'])->default('aberta');
            $table->enum('tipo', ['atendimento_avulso','sessao_completa','mensalidade']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('faturas');
    }
};