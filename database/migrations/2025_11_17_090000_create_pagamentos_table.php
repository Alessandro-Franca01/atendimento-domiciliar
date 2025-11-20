<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attendance_id')->nullable()->constrained('attendances')->onDelete('cascade');
            $table->foreignId('session_id')->nullable()->constrained('sessoes')->onDelete('cascade');
            $table->foreignId('patient_id')->constrained('patients')->onDelete('cascade');
            $table->foreignId('professional_id')->constrained('professionals')->onDelete('cascade');
            $table->foreignId('invoice_id')->nullable()->constrained('invoices')->onDelete('set null');
            $table->enum('metodo_pagamento', ['pix','dinheiro','cartao','transferencia']);
            $table->decimal('valor', 10, 2);
            $table->date('data_pagamento');
            $table->enum('status', ['pago','estornado','pendente'])->default('pago');
            $table->text('observacoes')->nullable();
            $table->timestamps();

            $table->unique(['attendance_id', 'data_pagamento']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
