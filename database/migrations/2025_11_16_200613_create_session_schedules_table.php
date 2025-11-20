<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('session_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('therapy_session_id')->constrained('therapy_sessions')->onDelete('cascade');
            $table->integer('dia_da_semana'); // 1=segunda, 2=terça, ..., 7=domingo
            $table->time('hora');
            $table->integer('duracao_minutos')->default(60);
            $table->foreignId('address_id')->constrained('addresses')->onDelete('cascade');
            $table->boolean('ativo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('session_schedules');
    }
};