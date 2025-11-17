<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('sessoes', function (Blueprint $table) {
            $table->decimal('desconto_valor', 10, 2)->nullable()->after('desconto_percentual');
        });
    }

    public function down(): void
    {
        Schema::table('sessoes', function (Blueprint $table) {
            $table->dropColumn('desconto_valor');
        });
    }
};