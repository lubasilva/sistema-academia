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
        Schema::create('workouts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('instructor_id')->constrained('users')->onDelete('cascade');
            $table->string('name'); // Nome do treino (Treino A, Treino Superior, etc)
            $table->text('description')->nullable();
            $table->string('type')->default('strength'); // strength, cardio, functional
            $table->integer('frequency_per_week')->default(1); // Quantas vezes por semana
            $table->boolean('is_active')->default(true);
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->timestamps();
            
            $table->index(['student_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workouts');
    }
};
