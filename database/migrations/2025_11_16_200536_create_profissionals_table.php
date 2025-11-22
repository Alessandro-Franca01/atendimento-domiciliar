<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('professionals', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('crefito')->unique();
            $table->string('cpf')->unique();
            $table->date('data_nascimento')->nullable();
            $table->string('foto')->nullable();
            $table->text('sobre')->nullable();
            $table->string('telefone');
            $table->text('especialidades')->nullable();
            $table->string('horario_funcionamento')->nullable();
            $table->enum('status', ['ativo', 'inativo'])->default('ativo');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('professionals');
    }
};