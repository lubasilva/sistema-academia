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
        Schema::create('bioimpedance_measurements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('measured_by')->constrained('users')->onDelete('cascade'); // Quem fez a medição
            
            // Medições básicas
            $table->decimal('weight', 5, 2); // Peso em kg
            $table->decimal('height', 5, 2)->nullable(); // Altura em cm
            $table->decimal('bmi', 4, 2)->nullable(); // IMC calculado
            
            // Composição corporal
            $table->decimal('body_fat_percentage', 5, 2)->nullable(); // % de gordura
            $table->decimal('muscle_mass', 5, 2)->nullable(); // Massa muscular em kg
            $table->decimal('bone_mass', 5, 2)->nullable(); // Massa óssea em kg
            $table->decimal('water_percentage', 5, 2)->nullable(); // % de água
            $table->decimal('visceral_fat', 3, 1)->nullable(); // Gordura visceral (nível)
            
            // Metabolismo
            $table->integer('basal_metabolic_rate')->nullable(); // Taxa metabólica basal
            $table->decimal('protein_percentage', 5, 2)->nullable(); // % de proteína
            
            // Medidas corporais (opcionais)
            $table->decimal('chest', 5, 2)->nullable(); // Peito/Busto cm
            $table->decimal('waist', 5, 2)->nullable(); // Cintura cm
            $table->decimal('hip', 5, 2)->nullable(); // Quadril cm
            $table->decimal('arm', 5, 2)->nullable(); // Braço cm
            $table->decimal('thigh', 5, 2)->nullable(); // Coxa cm
            
            // Observações
            $table->text('notes')->nullable();
            $table->date('measurement_date'); // Data da medição
            
            $table->timestamps();
            
            // Índices
            $table->index(['user_id', 'measurement_date']);
            $table->index('measured_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bioimpedance_measurements');
    }
};
