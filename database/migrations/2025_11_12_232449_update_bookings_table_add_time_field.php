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
        Schema::table('bookings', function (Blueprint $table) {
            // Adicionar campo time para armazenar o horário
            $table->time('time')->after('date')->nullable();
            
            // Tornar schedule_id opcional (para migração gradual)
            $table->foreignId('schedule_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn('time');
            // Note: não reverte o nullable do schedule_id para evitar perda de dados
        });
    }
};
