@extends('layouts.base')

@section('title', 'Novo Plano')

@section('content')
<div class="container-fluid py-4">
    <div class="mb-4">
        <h2 class="fw-bold">Novo Plano</h2>
        <p class="text-muted">Cadastre um novo plano de assinatura</p>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <form action="{{ route('plans.store') }}" method="POST">
                @csrf

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="name" class="form-label">Nome do Plano *</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                               id="name" name="name" value="{{ old('name') }}" required 
                               placeholder="Ex: Plano Mensal 3x, Plano Trimestral 5x">
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="price" class="form-label">Preço (R$) *</label>
                        <input type="number" step="0.01" class="form-control @error('price') is-invalid @enderror" 
                               id="price" name="price" value="{{ old('price') }}" required 
                               placeholder="0.00">
                        @error('price')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="frequency_per_week" class="form-label">Frequência Semanal *</label>
                        <select class="form-select @error('frequency_per_week') is-invalid @enderror" 
                                id="frequency_per_week" name="frequency_per_week" required>
                            <option value="">Selecione...</option>
                            <option value="1" {{ old('frequency_per_week') == 1 ? 'selected' : '' }}>1x por semana</option>
                            <option value="2" {{ old('frequency_per_week') == 2 ? 'selected' : '' }}>2x por semana</option>
                            <option value="3" {{ old('frequency_per_week') == 3 ? 'selected' : '' }}>3x por semana</option>
                            <option value="4" {{ old('frequency_per_week') == 4 ? 'selected' : '' }}>4x por semana</option>
                            <option value="5" {{ old('frequency_per_week') == 5 ? 'selected' : '' }}>5x por semana</option>
                        </select>
                        <small class="text-muted">Quantas vezes por semana o aluno pode treinar</small>
                        @error('frequency_per_week')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="billing_cycle" class="form-label">Ciclo de Cobrança *</label>
                        <select class="form-select @error('billing_cycle') is-invalid @enderror" 
                                id="billing_cycle" name="billing_cycle" required>
                            <option value="">Selecione...</option>
                            <option value="monthly" {{ old('billing_cycle') == 'monthly' ? 'selected' : '' }}>Mensal (30 dias)</option>
                            <option value="quarterly" {{ old('billing_cycle') == 'quarterly' ? 'selected' : '' }}>Trimestral (90 dias)</option>
                            <option value="semiannual" {{ old('billing_cycle') == 'semiannual' ? 'selected' : '' }}>Semestral (180 dias)</option>
                            <option value="annual" {{ old('billing_cycle') == 'annual' ? 'selected' : '' }}>Anual (365 dias)</option>
                        </select>
                        <small class="text-muted">Duração do plano</small>
                        @error('billing_cycle')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" 
                                   {{ old('is_active', true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">
                                Plano ativo (disponível para venda)
                            </label>
                        </div>
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary" id="submitBtn">
                        <i class="bi bi-check-circle"></i> Criar Plano
                    </button>
                    <a href="{{ route('plans.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-x-circle"></i> Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Proteção contra duplo clique
const form = document.querySelector('form');
const submitBtn = document.getElementById('submitBtn');

form.addEventListener('submit', function(e) {
    if (submitBtn.disabled) {
        e.preventDefault();
        return false;
    }
    
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Salvando...';
});
</script>
@endpush
@endsection
