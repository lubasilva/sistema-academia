@extends('layouts.base')

@section('title', $exercise->name)

@section('content')
<div class="container-fluid px-3 px-md-4">
    <!-- Header -->
    <div class="mb-4">
        <div class="d-flex align-items-center mb-2">
            <a href="{{ route('exercises.index') }}" class="btn btn-sm btn-outline-secondary me-3">
                <i class="bi bi-arrow-left"></i>
            </a>
            <div class="flex-grow-1">
                <h1 class="h4 mb-0 fw-bold">{{ $exercise->name }}</h1>
                <p class="text-muted mb-0 small">Detalhes do exercício</p>
            </div>
            <a href="{{ route('exercises.edit', $exercise) }}" class="btn btn-primary btn-sm">
                <i class="bi bi-pencil me-1"></i> Editar
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <!-- Informações Principais -->
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-body p-4">
                    @if($exercise->image_url)
                    <div class="mb-4 text-center">
                        <img src="{{ $exercise->image_url }}" 
                             alt="{{ $exercise->name }}" 
                             class="img-fluid rounded"
                             style="max-height: 400px;"
                             onerror="this.style.display='none'">
                    </div>
                    @endif

                    <h5 class="fw-bold mb-3">Descrição</h5>
                    @if($exercise->description)
                        <p class="text-muted">{{ $exercise->description }}</p>
                    @else
                        <p class="text-muted fst-italic">Sem descrição disponível</p>
                    @endif

                    <hr class="my-4">

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <h6 class="text-muted small mb-1">Grupo Muscular</h6>
                            <span class="badge bg-primary">
                                {{ \App\Models\Exercise::getMuscleGroups()[$exercise->muscle_group] ?? $exercise->muscle_group }}
                            </span>
                        </div>

                        @if($exercise->category)
                        <div class="col-md-6 mb-3">
                            <h6 class="text-muted small mb-1">Categoria</h6>
                            <span class="badge bg-info text-dark">
                                {{ ucfirst($exercise->category) }}
                            </span>
                        </div>
                        @endif

                        @if($exercise->equipment)
                        <div class="col-md-6 mb-3">
                            <h6 class="text-muted small mb-1">Equipamento</h6>
                            <span class="badge bg-secondary">
                                🛠️ {{ $exercise->equipment }}
                            </span>
                        </div>
                        @endif

                        <div class="col-md-6 mb-3">
                            <h6 class="text-muted small mb-1">Status</h6>
                            @if($exercise->is_active)
                                <span class="badge bg-success">✓ Ativo</span>
                            @else
                                <span class="badge bg-secondary">✗ Inativo</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Treinos que usam este exercício -->
            @if($exercise->workoutExercises->count() > 0)
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3">
                        <i class="bi bi-clipboard-data me-2"></i>
                        Usado em {{ $exercise->workoutExercises->count() }} treino(s)
                    </h5>
                    
                    <div class="list-group">
                        @foreach($exercise->workoutExercises->unique('workout_id') as $workoutExercise)
                            @if($workoutExercise->workout)
                            <a href="{{ route('workouts.show', $workoutExercise->workout) }}" 
                               class="list-group-item list-group-item-action">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1">{{ $workoutExercise->workout->name }}</h6>
                                        @if($workoutExercise->workout->student)
                                        <small class="text-muted">
                                            Aluno: {{ $workoutExercise->workout->student->name }}
                                        </small>
                                        @endif
                                    </div>
                                    <div class="text-muted small">
                                        {{ $workoutExercise->sets }} séries × {{ $workoutExercise->reps }} reps
                                    </div>
                                </div>
                            </a>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
        </div>

        <div class="col-md-4">
            <!-- Ações Rápidas -->
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-body p-3">
                    <h6 class="fw-bold mb-3">Ações</h6>
                    <div class="d-grid gap-2">
                        <a href="{{ route('exercises.edit', $exercise) }}" class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-pencil me-1"></i> Editar Exercício
                        </a>
                        
                        @if($exercise->workoutExercises->count() === 0)
                        <form method="POST" action="{{ route('exercises.destroy', $exercise) }}" 
                              onsubmit="return confirm('Tem certeza que deseja excluir este exercício?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger btn-sm w-100">
                                <i class="bi bi-trash me-1"></i> Excluir Exercício
                            </button>
                        </form>
                        @else
                        <button class="btn btn-outline-secondary btn-sm" disabled title="Não pode excluir pois está em uso">
                            <i class="bi bi-trash me-1"></i> Excluir (Em uso)
                        </button>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Informações Adicionais -->
            <div class="card border-0 shadow-sm">
                <div class="card-body p-3">
                    <h6 class="fw-bold mb-3">Informações</h6>
                    <div class="small text-muted">
                        <div class="mb-2">
                            <i class="bi bi-calendar-plus me-2"></i>
                            Criado em {{ $exercise->created_at->format('d/m/Y') }}
                        </div>
                        <div>
                            <i class="bi bi-calendar-check me-2"></i>
                            Atualizado em {{ $exercise->updated_at->format('d/m/Y') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
