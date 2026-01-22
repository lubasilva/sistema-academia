@extends('layouts.base')

@section('title', 'Treinos')

@section('content')
<div class="container-fluid px-3 px-md-4">
    <!-- Header Mobile-First -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h4 mb-1 fw-bold">💪 Meus Treinos</h1>
            <p class="text-muted mb-0 small">Acompanhe sua evolução</p>
        </div>
        
        @can('create', App\Models\Workout::class)
        <a href="{{ route('workouts.create') }}" class="btn btn-primary btn-sm rounded-pill">
            <i class="bi bi-plus-circle me-1"></i>
            <span class="d-none d-sm-inline">Novo</span>
        </a>
        @endcan
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Lista de Treinos -->
    @forelse($workouts as $workout)
        <div class="card border-0 shadow-sm mb-3 workout-card" onclick="window.location='{{ route('workouts.show', $workout) }}'">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div class="flex-grow-1">
                        <div class="d-flex align-items-center mb-2">
                            <span class="workout-type-badge {{ $workout->type }}">
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
                            <h5 class="mb-0 ms-2 fw-semibold">{{ $workout->name }}</h5>
                        </div>
                        
                        <!-- Informações do Aluno (apenas para admin/master) -->
                        @if(auth()->user()->role !== 'aluno')
                            <div class="mb-2">
                                <span class="badge bg-light text-dark border small">
                                    👤 {{ $workout->student->name }}
                                </span>
                            </div>
                        @endif
                        
                        <!-- Meta informações -->
                        <div class="d-flex flex-wrap gap-2 mb-2">
                            <small class="text-muted">
                                <i class="bi bi-calendar-week me-1"></i>{{ $workout->frequency_per_week }}x/semana
                            </small>
                            <small class="text-muted">
                                <i class="bi bi-collection me-1"></i>{{ $workout->exercises_count ?? $workout->exercises->count() }} exercícios
                            </small>
                            @if($workout->isInPeriod())
                                <span class="badge bg-success-subtle text-success border-success small">
                                    <i class="bi bi-play-fill me-1"></i>Ativo
                                </span>
                            @else
                                <span class="badge bg-secondary-subtle text-secondary border-secondary small">
                                    <i class="bi bi-pause-fill me-1"></i>Inativo
                                </span>
                            @endif
                        </div>
                        
                        <!-- Descrição -->
                        @if($workout->description)
                            <p class="text-muted small mb-2 line-clamp-2">{{ $workout->description }}</p>
                        @endif
                        
                        <!-- Período -->
                        <div class="text-muted small">
                            <i class="bi bi-calendar-range me-1"></i>
                            {{ $workout->start_date->format('d/m/Y') }}
                            @if($workout->end_date)
                                → {{ $workout->end_date->format('d/m/Y') }}
                            @else
                                → Indeterminado
                            @endif
                        </div>
                    </div>
                    
                    <!-- Actions -->
                    <div class="dropdown">
                        <button class="btn btn-link text-muted p-1" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-three-dots-vertical"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <a class="dropdown-item" href="{{ route('workouts.show', $workout) }}">
                                    <i class="bi bi-eye me-2"></i>Visualizar
                                </a>
                            </li>
                            @can('update', $workout)
                                <li>
                                    <a class="dropdown-item" href="{{ route('workouts.edit', $workout) }}">
                                        <i class="bi bi-pencil me-2"></i>Editar
                                    </a>
                                </li>
                            @endcan
                            @can('delete', $workout)
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form action="{{ route('workouts.destroy', $workout) }}" method="POST" class="d-inline" 
                                          onsubmit="return confirm('Tem certeza que deseja excluir este treino?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="dropdown-item text-danger">
                                            <i class="bi bi-trash me-2"></i>Excluir
                                        </button>
                                    </form>
                                </li>
                            @endcan
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <!-- Empty State -->
        <div class="text-center py-5">
            <div class="mb-4">
                <i class="bi bi-clipboard-data display-1 text-muted"></i>
            </div>
            <h5 class="text-muted mb-3">Nenhum treino encontrado</h5>
            @can('create', App\Models\Workout::class)
                <p class="text-muted mb-4">Comece criando um novo treino personalizado.</p>
                <a href="{{ route('workouts.create') }}" class="btn btn-primary rounded-pill">
                    <i class="bi bi-plus-circle me-2"></i>Criar Primeiro Treino
                </a>
            @else
                <p class="text-muted">Aguarde seu instrutor criar um treino para você.</p>
            @endcan
        </div>
    @endforelse

    <!-- Pagination -->
    @if($workouts->hasPages())
        <div class="d-flex justify-content-center mt-4">
            {{ $workouts->links() }}
        </div>
    @endif
</div>

@push('styles')
<style>
/* Mobile First Styles */
.workout-card {
    transition: all 0.2s ease;
    cursor: pointer;
    border-radius: 12px !important;
}

.workout-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15) !important;
}

.workout-type-badge {
    font-size: 1.2rem;
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 8px;
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

.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* Responsive improvements */
@media (max-width: 576px) {
    .container-fluid {
        padding-left: 15px !important;
        padding-right: 15px !important;
    }
    
    .workout-card {
        margin-bottom: 1rem !important;
        border-radius: 8px !important;
    }
    
    .card-body {
        padding: 1rem !important;
    }
}

/* Badges customizados */
.badge.bg-success-subtle {
    background-color: rgba(25, 135, 84, 0.1) !important;
}

.badge.bg-secondary-subtle {
    background-color: rgba(108, 117, 125, 0.1) !important;
}

.btn-primary.rounded-pill {
    border-radius: 25px !important;
    padding: 0.5rem 1rem;
}

/* Loading animation para cards */
.workout-card {
    animation: slideInUp 0.3s ease-out;
}

@keyframes slideInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Melhor contrast para dropdowns */
.dropdown-menu {
    border-radius: 8px;
    border: none;
    box-shadow: 0 4px 16px rgba(0,0,0,0.15);
}

.dropdown-item {
    border-radius: 6px;
    margin: 2px 4px;
    transition: all 0.2s ease;
}

.dropdown-item:hover {
    background: linear-gradient(45deg, #f8f9fa, #e9ecef);
}
</style>
@endpush
@endsection