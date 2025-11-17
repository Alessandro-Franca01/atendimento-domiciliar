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
        Schema::create('sessao_horarios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sessao_id')->constrained('sessoes');
            $table->integer('dia_da_semana'); // 1=segunda, 2=terça, ..., 7=domingo
            $table->time('hora');
            $table->integer('duracao_minutos')->default(60);
            $table->foreignId('endereco_id')->constrained('enderecos');
            $table->boolean('ativo')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sessao_horarios');
    }
};
