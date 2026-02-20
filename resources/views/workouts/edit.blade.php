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
                        <h6 class="text-primary fw-semibold">📊 Informações do Treino</h6>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info border-0">
                            <h6 class="alert-heading">ℹ️ Sobre os Exercícios</h6>
                            <p class="mb-0 small">
                                Para editar os exercícios, cargas e repetições, utilize a visualização do treino. 
                                Aqui você pode alterar apenas as informações gerais do treino.
                            </p>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Total de Exercícios</label>
                            <div class="input-group">
                                <span class="input-group-text">🎯</span>
                                <input type="text" class="form-control" value="{{ $workout->exercises->count() }} exercícios" disabled>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Criado por</label>
                            <div class="input-group">
                                <span class="input-group-text">👤</span>
                                <input type="text" class="form-control" value="{{ $workout->instructor->name }}" disabled>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Criado em</label>
                            <div class="input-group">
                                <span class="input-group-text">📅</span>
                                <input type="text" class="form-control" value="{{ $workout->created_at->format('d/m/Y H:i') }}" disabled>
                            </div>
                        </div>

                        @if($workout->exercises->count() > 0)
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Exercícios do Treino</label>
                            <div class="list-group">
                                @foreach($workout->exercises as $workoutExercise)
                                <div class="list-group-item">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h6 class="mb-1 small fw-semibold">{{ $workoutExercise->exercise->name }}</h6>
                                            <p class="mb-0 small text-muted">
                                                {{ $workoutExercise->sets }}x{{ $workoutExercise->reps }}
                                                @if($workoutExercise->initial_weight)
                                                    • {{ $workoutExercise->initial_weight }}kg
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
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
