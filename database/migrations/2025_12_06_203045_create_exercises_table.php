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
        Schema::create('exercises', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nome do exercício
            $table->text('description')->nullable(); // Descrição/instruções
            $table->string('muscle_group'); // Grupo muscular (peitoral, costas, etc)
            $table->string('category'); // Categoria (força, cardio, flexibilidade)
            $table->string('equipment')->nullable(); // Equipamento necessário
            $table->string('image_url')->nullable(); // URL da imagem demonstrativa
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index(['muscle_group', 'category']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exercises');
    }
};
