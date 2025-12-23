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
        Schema::create('workout_executions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workout_exercise_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Quem registrou
            $table->date('execution_date');
            $table->json('sets_data'); // Array com dados de cada série: [{"set": 1, "reps": 12, "weight": 15.5, "completed": true}]
            $table->text('notes')->nullable(); // Observações da execução
            $table->integer('total_volume')->nullable(); // Volume total (peso x reps x séries)
            $table->decimal('average_weight', 5, 2)->nullable(); // Peso médio usado
            $table->integer('duration_minutes')->nullable(); // Duração do exercício
            $table->timestamps();
            
            $table->index(['workout_exercise_id', 'execution_date']);
            $table->index(['user_id', 'execution_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workout_executions');
    }
};
