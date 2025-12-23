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
        Schema::table('user_plans', function (Blueprint $table) {
            // Créditos especiais de reposição (quando aluno falta ou desmarca)
            $table->unsignedInteger('extra_credits')->default(0)->after('credits_remaining');
            
            // Total de créditos já utilizados
            $table->unsignedInteger('total_credits_used')->default(0)->after('extra_credits');
            
            // Observações sobre o plano (ex: motivo de créditos extras)
            $table->text('observations')->nullable()->after('total_credits_used');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_plans', function (Blueprint $table) {
            $table->dropColumn(['extra_credits', 'total_credits_used', 'observations']);
        });
    }
};
