@extends('layouts.base')

@section('title', 'Planos')

@section('content')
<div class="container py-5">
    <div class="row mb-4">
        <div class="col text-center">
            <h2 class="fw-bold">Escolha seu Plano</h2>
            <p class="text-muted">Selecione o plano ideal para seus treinos</p>
        </div>
    </div>

    @if(!auth()->user()->phone)
    <div class="alert alert-warning mb-4">
        <i class="bi bi-exclamation-triangle"></i>
        <strong>Atenção!</strong> Para assinar um plano, você precisa informar seu telefone e CPF.
        Por favor, preencha os campos abaixo antes de escolher um plano.
    </div>
    @endif

    <div class="row g-4">
        @foreach(\App\Models\Plan::where('is_active', true)->get() as $plan)
        <div class="col-md-6 col-lg-4">
            <div class="card border-0 shadow-sm h-100 {{ $plan->is_featured ? 'border-primary border-2' : '' }}">
                @if($plan->is_featured)
                <div class="card-header bg-primary text-white text-center py-2">
                    <small class="fw-semibold">MAIS POPULAR</small>
                </div>
                @endif
                <div class="card-body text-center d-flex flex-column">
                    <h4 class="fw-bold mb-3">{{ $plan->name }}</h4>
                    <div class="mb-4">
                        <h2 class="fw-bold text-primary mb-0">
                            R$ {{ number_format($plan->price, 2, ',', '.') }}
                        </h2>
                        <small class="text-muted">{{ $plan->duration_days }} dias</small>
                    </div>
                    
                    <ul class="list-unstyled mb-4 flex-grow-1 text-start">
                        <li class="mb-2">
                            <i class="bi bi-check-circle-fill text-success"></i>
                            {{ $plan->credits }} créditos
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-check-circle-fill text-success"></i>
                            Válido por {{ $plan->duration_days }} dias
                        </li>
                        @if($plan->description)
                        <li class="mb-2 text-muted small">
                            {{ $plan->description }}
                        </li>
                        @endif
                    </ul>
                    
                    <button type="button" class="btn {{ $plan->is_featured ? 'btn-primary' : 'btn-outline-primary' }} w-100" 
                            data-bs-toggle="modal" data-bs-target="#paymentModal{{ $plan->id }}">
                        Assinar Agora
                    </button>
                </div>
            </div>
        </div>

        <!-- Modal de Pagamento -->
        <div class="modal fade" id="paymentModal{{ $plan->id }}" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Assinar {{ $plan->name }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="{{ route('payments.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="plan_id" value="{{ $plan->id }}">
                        
                        <div class="modal-body">
                            <div class="alert alert-info mb-3">
                                <strong>Plano:</strong> {{ $plan->name }}<br>
                                <strong>Valor:</strong> R$ {{ number_format($plan->price, 2, ',', '.') }}
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Telefone (WhatsApp) *</label>
                                <input type="text" name="phone" class="form-control" 
                                       value="{{ auth()->user()->phone }}" 
                                       placeholder="(11) 99999-9999" 
                                       maxlength="15"
                                       required>
                                <small class="text-muted">Formato: (11) 99999-9999</small>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">CPF</label>
                                <input type="text" name="cpf" class="form-control" 
                                       value="{{ auth()->user()->cpf }}" 
                                       placeholder="000.000.000-00"
                                       maxlength="14">
                                <small class="text-muted">Opcional, mas recomendado</small>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Forma de Pagamento *</label>
                                <select name="payment_method" class="form-select" required>
                                    <option value="pix">PIX (Aprovação Imediata)</option>
                                    <option value="boleto">Boleto (Vencimento em 3 dias)</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-credit-card"></i> Confirmar Pagamento
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>

<script>
// Máscaras de input
document.addEventListener('DOMContentLoaded', function() {
    // Máscara de telefone
    document.querySelectorAll('input[name="phone"]').forEach(input => {
        input.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length <= 11) {
                value = value.replace(/^(\d{2})(\d)/g, '($1) $2');
                value = value.replace(/(\d)(\d{4})$/, '$1-$2');
            }
            e.target.value = value;
        });
    });

    // Máscara de CPF
    document.querySelectorAll('input[name="cpf"]').forEach(input => {
        input.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length <= 11) {
                value = value.replace(/(\d{3})(\d)/, '$1.$2');
                value = value.replace(/(\d{3})(\d)/, '$1.$2');
                value = value.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
            }
            e.target.value = value;
        });
    });
});
</script>
@endsection
