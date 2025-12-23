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
        Schema::create('recurring_bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('day_of_week'); // monday, tuesday, etc
            $table->time('time'); // 08:00:00, 10:00:00, etc
            $table->boolean('is_active')->default(true); // se o padrão está ativo
            $table->date('last_created_date')->nullable(); // última data que foi criada uma reserva
            $table->timestamps();
            
            // Índice para buscar padrões ativos de um usuário
            $table->index(['user_id', 'is_active']);
        });
        
        // Adicionar campo na tabela bookings para relacionar com padrão recorrente
        Schema::table('bookings', function (Blueprint $table) {
            $table->foreignId('recurring_booking_id')->nullable()->constrained()->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropForeign(['recurring_booking_id']);
            $table->dropColumn('recurring_booking_id');
        });
        
        Schema::dropIfExists('recurring_bookings');
    }
};
