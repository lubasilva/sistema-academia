@extends('layouts.base')

@section('title', $workout->name)

@section('content')
<div class="container-fluid px-3 px-md-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <div class="d-flex align-items-center mb-2">
                <span class="workout-type-badge {{ $workout->type }} me-3">
                    @switch($workout->type)
                        @case('strength')
                            🏋️
                            @break
                        @case('cardio')
                            ❤️
                            @break
                        @case('functional')
                            🤸
                            @break
                        @default
                            💪
                    @endswitch
                </span>
                <div>
                    <h1 class="h4 mb-0 fw-bold">{{ $workout->name }}</h1>
                    <p class="text-muted mb-0 small">
                        Aluno: <strong>{{ $workout->student->name }}</strong> | 
                        Instrutor: <strong>{{ $workout->instructor->name }}</strong>
                    </p>
                </div>
            </div>
        </div>
        
        <div class="d-flex gap-2">
            @can('update', $workout)
                <a href="{{ route('workouts.edit', $workout) }}" class="btn btn-outline-primary btn-sm">
                    <i class="bi bi-pencil"></i>
                    <span class="d-none d-md-inline ms-1">Editar</span>
                </a>
            @endcan
            <a href="{{ route('workouts.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left"></i>
                <span class="d-none d-md-inline ms-1">Voltar</span>
            </a>
        </div>
    </div>

    <!-- Informações do Treino -->
    <div class="row mb-4">
        <div class="col-lg-8">
            <!-- Exercícios -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-semibold">🎯 Exercícios</h5>
                        <div>
                            <span class="badge bg-primary-subtle text-primary me-2">
                                {{ $workout->exercises->count() }} exercícios
                            </span>
                            @can('update', $workout)
                                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addExerciseModal">
                                    <i class="bi bi-plus-circle me-1"></i>Adicionar Exercício
                                </button>
                            @endcan
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    @forelse($workout->exercises as $index => $workoutExercise)
                        @if($workoutExercise->exercise)
                        <div class="p-3 border-bottom exercise-row" data-exercise-id="{{ $workoutExercise->id }}">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    <div class="d-flex align-items-center mb-2">
                                        <span class="badge bg-light text-dark border me-2">{{ $index + 1 }}</span>
                                        <h6 class="mb-0 fw-semibold">{{ $workoutExercise->exercise->name }}</h6>
                                    </div>
                                    
                                    <div class="row g-3 mb-2">
                                        <div class="col-6 col-md-3">
                                            <small class="text-muted d-block">Séries</small>
                                            <strong>{{ $workoutExercise->sets }}</strong>
                                        </div>
                                        <div class="col-6 col-md-3">
                                            <small class="text-muted d-block">Repetições</small>
                                            <strong>{{ $workoutExercise->reps }}</strong>
                                        </div>
                                        <div class="col-6 col-md-3">
                                            <small class="text-muted d-block">Peso Inicial</small>
                                            <strong>{{ $workoutExercise->initial_weight ? $workoutExercise->initial_weight . ' kg' : 'Peso corporal' }}</strong>
                                        </div>
                                        <div class="col-6 col-md-3">
                                            <small class="text-muted d-block">Descanso</small>
                                            <strong>{{ $workoutExercise->getFormattedRestTime() }}</strong>
                                        </div>
                                    </div>

                                    @if($workoutExercise->notes)
                                        <div class="mt-2">
                                            <small class="text-muted">
                                                <i class="bi bi-info-circle me-1"></i>
                                                {{ $workoutExercise->notes }}
                                            </small>
                                        </div>
                                    @endif

                                    <!-- Progresso (últimas execuções) -->
                                    @php
                                        $lastExecutions = $workoutExercise->executions()->latest('execution_date')->limit(3)->get();
                                    @endphp
                                    
                                    @if($lastExecutions->count() > 0)
                                        <div class="mt-3">
                                            <small class="text-muted fw-semibold d-block mb-2">📈 Últimas execuções:</small>
                                            <div class="d-flex gap-2 flex-wrap">
                                                @foreach($lastExecutions as $execution)
                                                    <span class="badge bg-success-subtle text-success border-success">
                                                        {{ $execution->execution_date->format('d/m') }}: 
                                                        {{ $execution->average_weight }}kg
                                                    </span>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                
                                <!-- Botões de ação -->
                                <div class="ms-2">
                                    <div class="btn-group" role="group">
                                        <button class="btn btn-primary btn-sm" onclick="openExecutionModal({{ $workoutExercise->id }})">
                                            <i class="bi bi-plus-circle"></i>
                                            <span class="d-none d-md-inline ms-1">Registrar</span>
                                        </button>
                                        @can('update', $workout)
                                            <button class="btn btn-outline-primary btn-sm" onclick="editExercise({{ $workoutExercise->id }}, '{{ $workoutExercise->sets }}', '{{ $workoutExercise->reps }}', '{{ $workoutExercise->initial_weight }}', '{{ $workoutExercise->rest_seconds }}', '{{ addslashes($workoutExercise->notes ?? '') }}')">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <button class="btn btn-outline-danger btn-sm" onclick="removeExercise({{ $workoutExercise->id }}, '{{ $workoutExercise->exercise->name }}')">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        @endcan
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                    @empty
                        <div class="p-4 text-center text-muted">
                            <i class="bi bi-clipboard-x display-6 mb-3"></i>
                            <p>Nenhum exercício adicionado ainda.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Informações Gerais -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-0">
                    <h6 class="mb-0 fw-semibold">📊 Informações Gerais</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <small class="text-muted d-block">Tipo</small>
                        <span class="badge bg-{{ $workout->type === 'strength' ? 'primary' : ($workout->type === 'cardio' ? 'danger' : 'warning') }}-subtle text-{{ $workout->type === 'strength' ? 'primary' : ($workout->type === 'cardio' ? 'danger' : 'warning') }} border">
                            @switch($workout->type)
                                @case('strength')
                                    🏋️ Força
                                    @break
                                @case('cardio')
                                    ❤️ Cardio
                                    @break
                                @case('functional')
                                    🤸 Funcional
                                    @break
                            @endswitch
                        </span>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted d-block">Frequência</small>
                        <strong>{{ $workout->frequency_per_week }}x por semana</strong>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted d-block">Período</small>
                        <div>
                            <strong>{{ $workout->start_date->format('d/m/Y') }}</strong>
                            @if($workout->end_date)
                                → <strong>{{ $workout->end_date->format('d/m/Y') }}</strong>
                            @else
                                → <em>Indeterminado</em>
                            @endif
                        </div>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted d-block">Status</small>
                        @if($workout->isInPeriod() && $workout->is_active)
                            <span class="badge bg-success-subtle text-success border-success">
                                <i class="bi bi-play-fill me-1"></i>Ativo
                            </span>
                        @else
                            <span class="badge bg-secondary-subtle text-secondary border-secondary">
                                <i class="bi bi-pause-fill me-1"></i>Inativo
                            </span>
                        @endif
                    </div>

                    @if($workout->description)
                        <div>
                            <small class="text-muted d-block">Descrição</small>
                            <p class="small mb-0">{{ $workout->description }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Resumo de Progresso -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0">
                    <h6 class="mb-0 fw-semibold">📈 Resumo de Progresso</h6>
                </div>
                <div class="card-body">
                    @php
                        $totalExecutions = $workout->executions()->count();
                        $exercisesWithProgress = $workout->exercises->filter(function($ex) {
                            return $ex->executions->count() > 0;
                        })->count();
                    @endphp
                    
                    <div class="mb-3">
                        <small class="text-muted d-block">Total de Execuções</small>
                        <strong>{{ $totalExecutions }}</strong>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted d-block">Exercícios com Progresso</small>
                        <strong>{{ $exercisesWithProgress }}/{{ $workout->exercises->count() }}</strong>
                        <div class="progress mt-1" style="height: 6px;">
                            <div class="progress-bar bg-success" style="width: {{ $workout->exercises->count() > 0 ? ($exercisesWithProgress / $workout->exercises->count()) * 100 : 0 }}%"></div>
                        </div>
                    </div>

                    @if($totalExecutions > 0)
                        <div>
                            <small class="text-muted d-block">Última Sessão</small>
                            @php
                                $lastExecution = $workout->executions()->latest('execution_date')->first();
                            @endphp
                            <strong>{{ $lastExecution ? $lastExecution->execution_date->format('d/m/Y') : 'N/A' }}</strong>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Adicionar Exercício -->
@can('update', $workout)
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
                            @php
                                $exercises = \App\Models\Exercise::active()
                                    ->orderBy('muscle_group')
                                    ->orderBy('name')
                                    ->get()
                                    ->groupBy('muscle_group');
                            @endphp
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
@endcan

@push('styles')
<style>
.workout-type-badge {
    font-size: 1.5rem;
    width: 48px;
    height: 48px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 12px;
    background: linear-gradient(45deg, #f8f9fa, #e9ecef);
}

.workout-type-badge.strength {
    background: linear-gradient(45deg, #e3f2fd, #bbdefb);
}

.workout-type-badge.cardio {
    background: linear-gradient(45deg, #fce4ec, #f8bbd9);
}

.workout-type-badge.functional {
    background: linear-gradient(45deg, #f3e5f5, #e1bee7);
}

.exercise-row {
    transition: background-color 0.2s ease;
}

.exercise-row:hover {
    background-color: #f8f9fa;
}

.exercise-row:last-child {
    border-bottom: none !important;
}

.card {
    border-radius: 12px !important;
}

.progress {
    border-radius: 3px;
}

/* Mobile responsivity */
@media (max-width: 768px) {
    .container-fluid {
        padding-left: 15px !important;
        padding-right: 15px !important;
    }
    
    .workout-type-badge {
        width: 40px;
        height: 40px;
        font-size: 1.2rem;
    }
}
</style>
@endpush

@push('scripts')
<script>
function openExecutionModal(workoutExerciseId) {
    // Por enquanto apenas um alert, mais tarde implementaremos o modal
    alert('Modal de registro de execução será implementado para o exercício ID: ' + workoutExerciseId);
}

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
@endsection