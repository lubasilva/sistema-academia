@extends('layouts.base')

@section('title', 'Planos')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold">Planos</h2>
            <p class="text-muted">Gerencie os planos de assinatura</p>
        </div>
        <a href="{{ route('plans.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Novo Plano
        </a>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="row g-4">
        @forelse($plans as $plan)
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100 {{ $plan->is_active ? '' : 'opacity-50' }}">
                <div class="card-body d-flex flex-column">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <h4 class="fw-bold mb-0">{{ $plan->name }}</h4>
                        @if($plan->is_active)
                            <span class="badge bg-success">Ativo</span>
                        @else
                            <span class="badge bg-secondary">Inativo</span>
                        @endif
                    </div>
                    
                    <p class="text-muted">{{ $plan->frequency_per_week }}x por semana - Plano {{ $plan->billing_cycle_name }}</p>
                    
                    <div class="my-3">
                        <h2 class="fw-bold text-primary mb-0">
                            R$ {{ number_format($plan->price, 2, ',', '.') }}
                        </h2>
                        <small class="text-muted">por {{ $plan->billing_cycle_name }}</small>
                    </div>
                    
                    <ul class="list-unstyled mb-4">
                        <li class="mb-2">
                            <i class="bi bi-check-circle-fill text-success me-2"></i>
                            {{ $plan->frequency_per_week }}x por semana
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-check-circle-fill text-success me-2"></i>
                            Válido por {{ $plan->duration_days }} dias
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-check-circle-fill text-success me-2"></i>
                            Acesso total ao estúdio
                        </li>
                    </ul>
                    
                    <div class="mt-auto d-flex gap-2">
                        <a href="{{ route('plans.edit', $plan) }}" class="btn btn-sm btn-outline-primary flex-grow-1">
                            <i class="bi bi-pencil"></i> Editar
                        </a>
                        <form action="{{ route('plans.destroy', $plan) }}" method="POST" class="flex-grow-1">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger w-100" onclick="return confirm('Tem certeza?')">
                                <i class="bi bi-trash"></i> Excluir
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center py-5">
                    <i class="bi bi-inbox display-1 text-muted"></i>
                    <p class="text-muted mt-3">Nenhum plano cadastrado</p>
                    <a href="{{ route('plans.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i> Criar Primeiro Plano
                    </a>
                </div>
            </div>
        </div>
        @endforelse
    </div>
</div>
@endsection
