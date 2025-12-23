@extends('layouts.base')

@section('title', 'Criar Treino')

@section('content')
<div class="container-fluid px-3 px-md-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h4 mb-1 fw-bold">💪 Novo Treino</h1>
            <p class="text-muted mb-0 small">Crie um treino personalizado</p>
        </div>
        <a href="{{ route('workouts.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i>Voltar
        </a>
    </div>

    <form action="{{ route('workouts.store') }}" method="POST" id="workoutForm">
        @csrf
        
        <div class="row">
            <!-- Informações Básicas -->
            <div class="col-lg-6 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-0 pb-0">
                        <h6 class="text-primary fw-semibold">📋 Informações Básicas</h6>
                    </div>
                    <div class="card-body">
                        <!-- Aluno -->
                        <div class="mb-3">
                            <label for="student_id" class="form-label fw-semibold">Aluno</label>
                            <select class="form-select" name="student_id" id="student_id" required>
                                <option value="">Selecione o aluno</option>
                                @foreach($students as $student)
                                    <option value="{{ $student->id }}" {{ old('student_id', $selectedStudentId) == $student->id ? 'selected' : '' }}>
                                        {{ $student->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('student_id')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Nome -->
                        <div class="mb-3">
                            <label for="name" class="form-label fw-semibold">Nome do Treino</label>
                            <input type="text" class="form-control" name="name" id="name" 
                                   value="{{ old('name') }}" placeholder="Ex: Treino A - Superior" required>
                            @error('name')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Tipo -->
                        <div class="mb-3">
                            <label for="type" class="form-label fw-semibold">Tipo de Treino</label>
                            <select class="form-select" name="type" id="type" required>
                                <option value="strength" {{ old('type') == 'strength' ? 'selected' : '' }}>🏋️ Força</option>
                                <option value="cardio" {{ old('type') == 'cardio' ? 'selected' : '' }}>❤️ Cardio</option>
                                <option value="functional" {{ old('type') == 'functional' ? 'selected' : '' }}>🤸 Funcional</option>
                            </select>
                            @error('type')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Frequência -->
                        <div class="mb-3">
                            <label for="frequency_per_week" class="form-label fw-semibold">Frequência Semanal</label>
                            <select class="form-select" name="frequency_per_week" id="frequency_per_week" required>
                                <option value="1" {{ old('frequency_per_week') == '1' ? 'selected' : '' }}>1x por semana</option>
                                <option value="2" {{ old('frequency_per_week') == '2' ? 'selected' : '' }}>2x por semana</option>
                                <option value="3" {{ old('frequency_per_week') == '3' ? 'selected' : '' }}>3x por semana</option>
                                <option value="4" {{ old('frequency_per_week') == '4' ? 'selected' : '' }}>4x por semana</option>
                                <option value="5" {{ old('frequency_per_week') == '5' ? 'selected' : '' }}>5x por semana</option>
                            </select>
                            @error('frequency_per_week')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Período -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="start_date" class="form-label fw-semibold">Data de Início</label>
                                <input type="date" class="form-control" name="start_date" id="start_date" 
                                       value="{{ old('start_date', date('Y-m-d')) }}" required>
                                @error('start_date')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="end_date" class="form-label fw-semibold">Data de Fim (opcional)</label>
                                <input type="date" class="form-control" name="end_date" id="end_date" 
                                       value="{{ old('end_date') }}">
                                @error('end_date')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Descrição -->
                        <div class="mb-3">
                            <label for="description" class="form-label fw-semibold">Descrição (opcional)</label>
                            <textarea class="form-control" name="description" id="description" rows="3" 
                                      placeholder="Objetivo do treino, observações gerais...">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Exercícios -->
            <div class="col-lg-6 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-0 pb-0">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="text-primary fw-semibold">🎯 Exercícios</h6>
                            <button type="button" class="btn btn-primary btn-sm" onclick="addExercise()">
                                <i class="bi bi-plus-circle me-1"></i>Adicionar
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="exercises-container">
                            <!-- Exercícios serão adicionados aqui -->
                        </div>
                        
                        @error('exercises')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Botões -->
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('workouts.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-x-circle me-1"></i>Cancelar
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle me-1"></i>Salvar Treino
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Template para Exercício -->
<template id="exercise-template">
    <div class="exercise-item border rounded p-3 mb-3" style="background: #f8f9fa;">
        <div class="d-flex justify-content-between align-items-start mb-2">
            <h6 class="mb-0 fw-semibold text-dark">Exercício ##INDEX##</h6>
            <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeExercise(this)">
                <i class="bi bi-trash"></i>
            </button>
        </div>
        
        <div class="row">
            <div class="col-12 mb-3">
                <label class="form-label small fw-semibold">Exercício</label>
                <select class="form-select" name="exercises[##INDEX##][exercise_id]" required>
                    <option value="">Selecione o exercício</option>
                    @foreach($exercises as $muscleGroup => $groupExercises)
                        <optgroup label="{{ ucfirst($muscleGroup) }}">
                            @foreach($groupExercises as $exercise)
                                <option value="{{ $exercise->id }}">{{ $exercise->name }}</option>
                            @endforeach
                        </optgroup>
                    @endforeach
                </select>
            </div>
            
            <div class="col-6 col-md-3 mb-3">
                <label class="form-label small fw-semibold">Séries</label>
                <input type="number" class="form-control" name="exercises[##INDEX##][sets]" min="1" value="3" required>
            </div>
            
            <div class="col-6 col-md-3 mb-3">
                <label class="form-label small fw-semibold">Reps</label>
                <input type="text" class="form-control" name="exercises[##INDEX##][reps]" value="12" placeholder="Ex: 12, 8-10" required>
            </div>
            
            <div class="col-6 col-md-3 mb-3">
                <label class="form-label small fw-semibold">Peso (kg)</label>
                <input type="number" class="form-control" name="exercises[##INDEX##][initial_weight]" min="0" step="0.5" placeholder="0">
            </div>
            
            <div class="col-6 col-md-3 mb-3">
                <label class="form-label small fw-semibold">Descanso (s)</label>
                <input type="number" class="form-control" name="exercises[##INDEX##][rest_seconds]" min="10" value="60" required>
            </div>
            
            <div class="col-12">
                <label class="form-label small fw-semibold">Observações (opcional)</label>
                <input type="text" class="form-control" name="exercises[##INDEX##][notes]" placeholder="Observações sobre execução, carga, etc...">
            </div>
        </div>
    </div>
</template>

@push('styles')
<style>
.exercise-item {
    transition: all 0.2s ease;
}

.exercise-item:hover {
    background-color: #e9ecef !important;
}

.card {
    border-radius: 12px !important;
}

.btn {
    border-radius: 8px !important;
}

/* Mobile adjustments */
@media (max-width: 768px) {
    .container-fluid {
        padding-left: 15px !important;
        padding-right: 15px !important;
    }
    
    .card-body {
        padding: 1rem !important;
    }
}
</style>
@endpush

@push('scripts')
<script>
let exerciseIndex = 0;

function addExercise() {
    const container = document.getElementById('exercises-container');
    const template = document.getElementById('exercise-template');
    const content = template.content.cloneNode(true);
    
    // Replace placeholders
    const html = content.querySelector('.exercise-item').outerHTML.replace(/##INDEX##/g, exerciseIndex);
    container.insertAdjacentHTML('beforeend', html);
    
    exerciseIndex++;
}

function removeExercise(button) {
    if (confirm('Deseja remover este exercício?')) {
        button.closest('.exercise-item').remove();
    }
}

// Add first exercise on page load
document.addEventListener('DOMContentLoaded', function() {
    addExercise();
});

// Form validation
document.getElementById('workoutForm').addEventListener('submit', function(e) {
    const exercises = document.querySelectorAll('.exercise-item');
    if (exercises.length === 0) {
        e.preventDefault();
        alert('Adicione pelo menos um exercício ao treino.');
        return false;
    }
});
</script>
@endpush
@endsection