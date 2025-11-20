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
        Schema::table('professionals', function (Blueprint $table) {
            $table->string('email')->unique()->after('nome');
            $table->string('password')->after('email');
            $table->string('cpf')->unique()->after('crefito');
            $table->date('data_nascimento')->nullable()->after('cpf');
            $table->string('foto')->nullable()->after('data_nascimento');
            $table->text('sobre')->nullable()->after('foto');
            $table->rememberToken()->after('password');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('professionals', function (Blueprint $table) {
            $table->dropColumn([
                'email', 
                'password', 
                'cpf', 
                'data_nascimento', 
                'foto', 
                'sobre', 
                'remember_token'
            ]);
        });
    }
};