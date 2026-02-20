@extends('layouts.base')

@section('title', 'Editar Treino')

@section('content')
<div class="container-fluid px-3 px-md-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h4 mb-1 fw-bold">💪 Editar Treino</h1>
            <p class="text-muted mb-0 small">{{ $workout->name }}</p>
        </div>
        <a href="{{ route('workouts.show', $workout) }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i>Voltar
        </a>
    </div>

    <form action="{{ route('workouts.update', $workout) }}" method="POST" id="workoutForm">
        @csrf
        @method('PUT')
        
        <div class="row">
            <!-- Informações Básicas -->
            <div class="col-lg-6 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-0 pb-0">
                        <h6 class="text-primary fw-semibold">📋 Informações Básicas</h6>
                    </div>
                    <div class="card-body">
                        <!-- Aluno (somente leitura) -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Aluno</label>
                            <input type="text" class="form-control" value="{{ $workout->student->name }}" disabled>
                            <small class="text-muted">O aluno não pode ser alterado após criar o treino</small>
                        </div>

                        <!-- Nome -->
                        <div class="mb-3">
                            <label for="name" class="form-label fw-semibold">Nome do Treino</label>
                            <input type="text" class="form-control" name="name" id="name" 
                                   value="{{ old('name', $workout->name) }}" placeholder="Ex: Treino A - Superior" required>
                            @error('name')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Tipo -->
                        <div class="mb-3">
                            <label for="type" class="form-label fw-semibold">Tipo de Treino</label>
                            <select class="form-select" name="type" id="type" required>
                                <option value="strength" {{ old('type', $workout->type) == 'strength' ? 'selected' : '' }}>🏋️ Força</option>
                                <option value="cardio" {{ old('type', $workout->type) == 'cardio' ? 'selected' : '' }}>❤️ Cardio</option>
                                <option value="functional" {{ old('type', $workout->type) == 'functional' ? 'selected' : '' }}>🤸 Funcional</option>
                            </select>
                            @error('type')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Frequência -->
                        <div class="mb-3">
                            <label for="frequency_per_week" class="form-label fw-semibold">Frequência Semanal</label>
                            <select class="form-select" name="frequency_per_week" id="frequency_per_week" required>
                                <option value="1" {{ old('frequency_per_week', $workout->frequency_per_week) == '1' ? 'selected' : '' }}>1x por semana</option>
                                <option value="2" {{ old('frequency_per_week', $workout->frequency_per_week) == '2' ? 'selected' : '' }}>2x por semana</option>
                                <option value="3" {{ old('frequency_per_week', $workout->frequency_per_week) == '3' ? 'selected' : '' }}>3x por semana</option>
                                <option value="4" {{ old('frequency_per_week', $workout->frequency_per_week) == '4' ? 'selected' : '' }}>4x por semana</option>
                                <option value="5" {{ old('frequency_per_week', $workout->frequency_per_week) == '5' ? 'selected' : '' }}>5x por semana</option>
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
                                       value="{{ old('start_date', $workout->start_date?->format('Y-m-d')) }}" required>
                                @error('start_date')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="end_date" class="form-label fw-semibold">Data de Fim (opcional)</label>
                                <input type="date" class="form-control" name="end_date" id="end_date" 
                                       value="{{ old('end_date', $workout->end_date?->format('Y-m-d')) }}">
                                @error('end_date')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Status Ativo -->
                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" 
                                       {{ old('is_active', $workout->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="is_active">
                                    Treino Ativo
                                </label>
                            </div>
                            <small class="text-muted">Treinos inativos não aparecem para o aluno</small>
                        </div>

                        <!-- Descrição -->
                        <div class="mb-3">
                            <label for="description" class="form-label fw-semibold">Descrição (opcional)</label>
                            <textarea class="form-control" name="description" id="description" rows="3" 
                                      placeholder="Objetivo do treino, observações gerais...">{{ old('description', $workout->description) }}</textarea>
                            @error('description')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Informações Adicionais -->
            <div class="col-lg-6 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-0 pb-0">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="text-primary fw-semibold">🎯 Exercícios do Treino</h6>
                            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addExerciseModal">
                                <i class="bi bi-plus-circle me-1"></i>Adicionar
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        @if($workout->exercises->count() > 0)
                        <div class="list-group">
                            @foreach($workout->exercises as $workoutExercise)
                            <div class="list-group-item">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1 small fw-semibold">{{ $workoutExercise->exercise->name }}</h6>
                                        <p class="mb-0 small text-muted">
                                            {{ $workoutExercise->sets }}x{{ $workoutExercise->reps }}
                                            @if($workoutExercise->initial_weight)
                                                • {{ $workoutExercise->initial_weight }}kg
                                            @endif
                                            • {{ floor($workoutExercise->rest_seconds / 60) }}:{{ str_pad($workoutExercise->rest_seconds % 60, 2, '0', STR_PAD_LEFT) }}
                                        </p>
                                    </div>
                                    <div class="btn-group btn-group-sm ms-2">
                                        <button type="button" class="btn btn-outline-primary" onclick="editExercise({{ $workoutExercise->id }}, '{{ $workoutExercise->sets }}', '{{ $workoutExercise->reps }}', '{{ $workoutExercise->initial_weight }}', '{{ $workoutExercise->rest_seconds }}', '{{ addslashes($workoutExercise->notes ?? '') }}')">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button type="button" class="btn btn-outline-danger" onclick="removeExercise({{ $workoutExercise->id }}, '{{ $workoutExercise->exercise->name }}')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @else
                        <div class="alert alert-info border-0 mb-0">
                            <p class="mb-0 small">
                                <i class="bi bi-info-circle me-1"></i>
                                Nenhum exercício adicionado ainda. Clique em "Adicionar" para incluir exercícios neste treino.
                            </p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Botões -->
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('workouts.show', $workout) }}" class="btn btn-outline-secondary">
                        <i class="bi bi-x-circle me-1"></i>Cancelar
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle me-1"></i>Atualizar Treino
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Modal: Adicionar Exercício -->
<div class="modal fade" id="addExerciseModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('workouts.exercises.add', $workout) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">➕ Adicionar Exercício</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="exercise_id" class="form-label fw-semibold">Exercício</label>
                        <select class="form-select" name="exercise_id" id="exercise_id" required>
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
                    
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label for="sets" class="form-label fw-semibold">Séries</label>
                            <input type="number" class="form-control" name="sets" id="sets" min="1" value="3" required>
                        </div>
                        <div class="col-6 mb-3">
                            <label for="reps" class="form-label fw-semibold">Repetições</label>
                            <input type="text" class="form-control" name="reps" id="reps" value="12" placeholder="Ex: 12, 8-10" required>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label for="initial_weight" class="form-label fw-semibold">Peso (kg)</label>
                            <input type="number" class="form-control" name="initial_weight" id="initial_weight" min="0" step="0.5" placeholder="0">
                        </div>
                        <div class="col-6 mb-3">
                            <label for="rest_seconds" class="form-label fw-semibold">Descanso (s)</label>
                            <input type="number" class="form-control" name="rest_seconds" id="rest_seconds" min="10" value="60" required>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="notes" class="form-label fw-semibold">Observações</label>
                        <textarea class="form-control" name="notes" id="notes" rows="2" placeholder="Observações sobre execução, carga, etc..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Adicionar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: Editar Exercício -->
<div class="modal fade" id="editExerciseModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="editExerciseForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">✏️ Editar Exercício</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label for="edit_sets" class="form-label fw-semibold">Séries</label>
                            <input type="number" class="form-control" name="sets" id="edit_sets" min="1" required>
                        </div>
                        <div class="col-6 mb-3">
                            <label for="edit_reps" class="form-label fw-semibold">Repetições</label>
                            <input type="text" class="form-control" name="reps" id="edit_reps" required>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label for="edit_initial_weight" class="form-label fw-semibold">Peso (kg)</label>
                            <input type="number" class="form-control" name="initial_weight" id="edit_initial_weight" min="0" step="0.5">
                        </div>
                        <div class="col-6 mb-3">
                            <label for="edit_rest_seconds" class="form-label fw-semibold">Descanso (s)</label>
                            <input type="number" class="form-control" name="rest_seconds" id="edit_rest_seconds" min="10" required>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_notes" class="form-label fw-semibold">Observações</label>
                        <textarea class="form-control" name="notes" id="edit_notes" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Form oculto para remover exercício -->
<form id="removeExerciseForm" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

@push('scripts')
<script>
function editExercise(exerciseId, sets, reps, weight, rest, notes) {
    document.getElementById('edit_sets').value = sets;
    document.getElementById('edit_reps').value = reps;
    document.getElementById('edit_initial_weight').value = weight || '';
    document.getElementById('edit_rest_seconds').value = rest;
    document.getElementById('edit_notes').value = notes || '';
    
    const form = document.getElementById('editExerciseForm');
    form.action = "{{ route('workouts.exercises.update', [$workout, '__ID__']) }}".replace('__ID__', exerciseId);
    
    const modal = new bootstrap.Modal(document.getElementById('editExerciseModal'));
    modal.show();
}

function removeExercise(exerciseId, exerciseName) {
    if (confirm(`Deseja realmente remover o exercício "${exerciseName}" deste treino?`)) {
        const form = document.getElementById('removeExerciseForm');
        form.action = "{{ route('workouts.exercises.remove', [$workout, '__ID__']) }}".replace('__ID__', exerciseId);
        form.submit();
    }
}
</script>
@endpush

@push('styles')
<style>
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
@endsection
