<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('invoice_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained('invoices')->onDelete('cascade');
            $table->string('descricao');
            $table->integer('quantidade')->default(1);
            $table->decimal('valor_unitario', 10, 2);
            $table->decimal('valor_total', 10, 2);
            $table->foreignId('attendance_id')->nullable()->constrained('attendances')->onDelete('set null');
            $table->foreignId('session_id')->nullable()->constrained('sessoes')->onDelete('set null');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoice_items');
    }
};
