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
        Schema::create('workout_exercises', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workout_id')->constrained()->onDelete('cascade');
            $table->foreignId('exercise_id')->constrained()->onDelete('cascade');
            $table->integer('order_in_workout'); // Ordem do exercício no treino
            $table->integer('sets'); // Número de séries
            $table->string('reps'); // Repetições (pode ser "12", "8-10", "até a falha")
            $table->decimal('initial_weight', 5, 2)->nullable(); // Peso inicial sugerido
            $table->integer('rest_seconds')->default(60); // Tempo de descanso em segundos
            $table->text('notes')->nullable(); // Observações específicas
            $table->timestamps();
            
            $table->index(['workout_id', 'order_in_workout']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workout_exercises');
    }
};
