<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('appointment_id')->constrained('appointments')->onDelete('cascade');
            $table->foreignId('professional_id')->constrained('professionals')->onDelete('cascade');
            $table->foreignId('patient_id')->constrained('patients')->onDelete('cascade');
            $table->dateTime('data_realizacao');
            $table->decimal('valor', 10, 2)->nullable();
            $table->text('evolucao');
            $table->text('procedimento_realizado');
            $table->text('assinatura_paciente')->nullable();
            $table->enum('status', ['concluido', 'interrompido'])->default('concluido');
            $table->enum('status_pagamento', ['pendente', 'pago', 'pago_via_sessao', 'estornado'])->default('pendente');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};