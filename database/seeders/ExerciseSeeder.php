<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Exercise;

class ExerciseSeeder extends Seeder
{
    public function run(): void
    {
        $exercises = [
            // Peitoral
            ['name' => 'Supino Reto', 'muscle_group' => 'peitoral', 'category' => 'strength', 'equipment' => 'Barra/Halteres', 'description' => 'Exercício básico para desenvolvimento do peitoral'],
            ['name' => 'Supino Inclinado', 'muscle_group' => 'peitoral', 'category' => 'strength', 'equipment' => 'Banco inclinado', 'description' => 'Foco na parte superior do peitoral'],
            ['name' => 'Flexão de Braço', 'muscle_group' => 'peitoral', 'category' => 'strength', 'equipment' => 'Peso corporal', 'description' => 'Exercício funcional usando peso corporal'],
            
            // Costas
            ['name' => 'Puxada Alta', 'muscle_group' => 'costas', 'category' => 'strength', 'equipment' => 'Polia alta', 'description' => 'Desenvolvimento do latíssimo do dorso'],
            ['name' => 'Remada Curvada', 'muscle_group' => 'costas', 'category' => 'strength', 'equipment' => 'Barra/Halteres', 'description' => 'Fortalecimento do meio das costas'],
            ['name' => 'Levantamento Terra', 'muscle_group' => 'costas', 'category' => 'strength', 'equipment' => 'Barra', 'description' => 'Exercício composto para costas e posteriores'],
            
            // Ombros
            ['name' => 'Desenvolvimento Militar', 'muscle_group' => 'ombros', 'category' => 'strength', 'equipment' => 'Barra/Halteres', 'description' => 'Desenvolvimento completo dos deltoides'],
            ['name' => 'Elevação Lateral', 'muscle_group' => 'ombros', 'category' => 'strength', 'equipment' => 'Halteres', 'description' => 'Isolamento do deltoide médio'],
            ['name' => 'Elevação Frontal', 'muscle_group' => 'ombros', 'category' => 'strength', 'equipment' => 'Halteres', 'description' => 'Trabalho do deltoide anterior'],
            
            // Braços
            ['name' => 'Rosca Direta', 'muscle_group' => 'biceps', 'category' => 'strength', 'equipment' => 'Barra/Halteres', 'description' => 'Exercício básico para bíceps'],
            ['name' => 'Rosca Martelo', 'muscle_group' => 'biceps', 'category' => 'strength', 'equipment' => 'Halteres', 'description' => 'Trabalha bíceps e antebraço'],
            ['name' => 'Tríceps Testa', 'muscle_group' => 'triceps', 'category' => 'strength', 'equipment' => 'Barra/Halteres', 'description' => 'Isolamento do tríceps'],
            ['name' => 'Mergulho', 'muscle_group' => 'triceps', 'category' => 'strength', 'equipment' => 'Paralelas', 'description' => 'Exercício composto para tríceps'],
            
            // Pernas
            ['name' => 'Agachamento', 'muscle_group' => 'pernas', 'category' => 'strength', 'equipment' => 'Barra', 'description' => 'Exercício fundamental para pernas'],
            ['name' => 'Leg Press', 'muscle_group' => 'pernas', 'category' => 'strength', 'equipment' => 'Máquina leg press', 'description' => 'Desenvolvimento seguro dos quadríceps'],
            ['name' => 'Extensão de Pernas', 'muscle_group' => 'pernas', 'category' => 'strength', 'equipment' => 'Máquina extensora', 'description' => 'Isolamento dos quadríceps'],
            ['name' => 'Flexão de Pernas', 'muscle_group' => 'pernas', 'category' => 'strength', 'equipment' => 'Máquina flexora', 'description' => 'Trabalho dos isquiotibiais'],
            ['name' => 'Panturrilha em Pé', 'muscle_group' => 'pernas', 'category' => 'strength', 'equipment' => 'Máquina/Halteres', 'description' => 'Desenvolvimento das panturrilhas'],
            
            // Glúteos
            ['name' => 'Hip Thrust', 'muscle_group' => 'gluteos', 'category' => 'strength', 'equipment' => 'Banco/Barra', 'description' => 'Exercício específico para glúteos'],
            ['name' => 'Agachamento Búlgaro', 'muscle_group' => 'gluteos', 'category' => 'strength', 'equipment' => 'Banco/Halteres', 'description' => 'Unilateral com foco em glúteos'],
            
            // Core
            ['name' => 'Prancha', 'muscle_group' => 'core', 'category' => 'strength', 'equipment' => 'Peso corporal', 'description' => 'Isometria para core'],
            ['name' => 'Abdominal Crunch', 'muscle_group' => 'core', 'category' => 'strength', 'equipment' => 'Peso corporal', 'description' => 'Exercício básico para abdômen'],
            ['name' => 'Elevação de Pernas', 'muscle_group' => 'core', 'category' => 'strength', 'equipment' => 'Peso corporal', 'description' => 'Trabalho do abdômen inferior'],
            
            // Cardio
            ['name' => 'Esteira', 'muscle_group' => 'cardio', 'category' => 'cardio', 'equipment' => 'Esteira', 'description' => 'Exercício cardiovascular'],
            ['name' => 'Bicicleta Ergométrica', 'muscle_group' => 'cardio', 'category' => 'cardio', 'equipment' => 'Bicicleta', 'description' => 'Cardio de baixo impacto'],
            ['name' => 'Elíptico', 'muscle_group' => 'cardio', 'category' => 'cardio', 'equipment' => 'Elíptico', 'description' => 'Exercício cardiovascular completo'],
        ];

        foreach ($exercises as $exercise) {
            Exercise::create($exercise);
        }
    }
}
