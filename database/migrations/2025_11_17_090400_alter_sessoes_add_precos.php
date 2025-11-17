<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('sessoes', function (Blueprint $table) {
            $table->decimal('valor_por_sessao', 10, 2)->nullable()->after('total_sessoes');
            $table->decimal('desconto_percentual', 5, 2)->default(0)->after('valor_por_sessao');
        });
    }

    public function down(): void
    {
        Schema::table('sessoes', function (Blueprint $table) {
            $table->dropColumn(['valor_por_sessao', 'desconto_percentual']);
        });
    }
};