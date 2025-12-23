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
        Schema::create('schedule_blocks', function (Blueprint $table) {
            $table->id();
            $table->date('date'); // Data específica do bloqueio
            $table->time('start_time'); // Hora de início do bloqueio
            $table->time('end_time'); // Hora de fim do bloqueio
            $table->enum('reason', ['maintenance', 'holiday', 'event', 'other'])->default('other');
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            
            // Índices para performance
            $table->index('date');
            $table->index(['date', 'start_time', 'end_time']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedule_blocks');
    }
};
