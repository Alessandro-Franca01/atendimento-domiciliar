<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('fatura_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fatura_id')->constrained('faturas')->onDelete('cascade');
            $table->string('descricao');
            $table->integer('quantidade')->default(1);
            $table->decimal('valor_unitario', 10, 2);
            $table->decimal('valor_total', 10, 2);
            $table->foreignId('atendimento_id')->nullable()->constrained('atendimentos')->onDelete('set null');
            $table->foreignId('sessao_id')->nullable()->constrained('sessoes')->onDelete('set null');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fatura_items');
    }
};