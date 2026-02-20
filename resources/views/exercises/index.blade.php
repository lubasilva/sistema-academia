@extends('layouts.base')

@section('title', 'Exercícios')

@section('content')
<div class="container-fluid px-3 px-md-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h4 mb-1 fw-bold">🏋️ Biblioteca de Exercícios</h1>
            <p class="text-muted mb-0 small">Gerencie todos os exercícios disponíveis</p>
        </div>
        
        <a href="{{ route('exercises.create') }}" class="btn btn-primary btn-sm rounded-pill">
            <i class="bi bi-plus-circle me-1"></i>
            <span class="d-none d-sm-inline">Novo Exercício</span>
        </a>
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

    <!-- Filtros -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('exercises.index') }}" class="row g-2">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control form-control-sm" 
                           placeholder="🔍 Buscar exercício..." value="{{ $search ?? '' }}">
                </div>
                <div class="col-md-3">
                    <select name="muscle_group" class="form-select form-select-sm">
                        <option value="">Todos os Grupos Musculares</option>
                        @foreach($muscleGroups as $key => $label)
                            <option value="{{ $key }}" {{ ($muscleGroup ?? '') == $key ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="status" class="form-select form-select-sm">
                        <option value="all" {{ ($status ?? 'all') == 'all' ? 'selected' : '' }}>Todos</option>
                        <option value="active" {{ ($status ?? 'all') == 'active' ? 'selected' : '' }}>Ativos</option>
                        <option value="inactive" {{ ($status ?? 'all') == 'inactive' ? 'selected' : '' }}>Inativos</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-sm btn-primary flex-grow-1">
                        <i class="bi bi-funnel"></i> Filtrar
                    </button>
                    <a href="{{ route('exercises.index') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-x-circle"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Lista de Exercícios -->
    @forelse($exercises as $exercise)
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="flex-grow-1">
                        <div class="d-flex align-items-center mb-2">
                            <h5 class="mb-0 fw-semibold">{{ $exercise->name }}</h5>
                            @if(!$exercise->is_active)
                                <span class="badge bg-secondary ms-2">Inativo</span>
                            @endif
                        </div>
                        
                        @if($exercise->description)
                            <p class="text-muted small mb-2">{{ Str::limit($exercise->description, 100) }}</p>
                        @endif
                        
                        <div class="d-flex flex-wrap gap-2">
                            <span class="badge bg-primary">
                                {{ $muscleGroups[$exercise->muscle_group] ?? $exercise->muscle_group }}
                            </span>
                            @if($exercise->category)
                                <span class="badge bg-info text-dark">
                                    {{ ucfirst($exercise->category) }}
                                </span>
                            @endif
                            @if($exercise->equipment)
                                <span class="badge bg-secondary">
                                    🛠️ {{ $exercise->equipment }}
                                </span>
                            @endif
                        </div>
                    </div>
                    
                    <div class="d-flex gap-2 ms-3">
                        <a href="{{ route('exercises.edit', $exercise) }}" 
                           class="btn btn-sm btn-outline-primary rounded-circle" 
                           title="Editar">
                            <i class="bi bi-pencil"></i>
                        </a>
                        
                        <form method="POST" action="{{ route('exercises.destroy', $exercise) }}" 
                              onsubmit="return confirm('Tem certeza que deseja excluir este exercício?')" 
                              class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger rounded-circle" title="Excluir">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="text-center py-5">
            <div class="mb-3">
                <i class="bi bi-inbox" style="font-size: 3rem; color: #dee2e6;"></i>
            </div>
            <h5 class="text-muted">Nenhum exercício encontrado</h5>
            <p class="text-muted small">
                @if($search || $muscleGroup || $status !== 'all')
                    Tente ajustar os filtros ou 
                @endif
                <a href="{{ route('exercises.create') }}">crie um novo exercício</a>
            </p>
        </div>
    @endforelse

    <!-- Paginação -->
    @if($exercises->hasPages())
        <div class="d-flex justify-content-center mt-4">
            {{ $exercises->links() }}
        </div>
    @endif
</div>

<style>
.card {
    transition: transform 0.2s, box-shadow 0.2s;
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 .5rem 1rem rgba(0,0,0,.15) !important;
}
</style>
@endsection
