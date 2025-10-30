@extends('layouts.base')

@section('title', 'Detalhes do Pagamento')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 pt-4">
                    <h4 class="fw-bold mb-0">Detalhes do Pagamento</h4>
                </div>
                <div class="card-body p-4">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6 class="text-muted small mb-1">Plano</h6>
                            <p class="fw-semibold">{{ $payment->plan->name }}</p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted small mb-1">Valor</h6>
                            <p class="fw-semibold">R$ {{ number_format($payment->amount, 2, ',', '.') }}</p>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6 class="text-muted small mb-1">Data de Criação</h6>
                            <p>{{ $payment->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted small mb-1">Vencimento</h6>
                            <p>{{ $payment->due_date ? $payment->due_date->format('d/m/Y') : '-' }}</p>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col">
                            <h6 class="text-muted small mb-1">Status</h6>
                            @if($payment->status === 'paid')
                                <span class="badge bg-success fs-6">Pago</span>
                                @if($payment->paid_at)
                                <p class="small text-muted mt-1">Pago em {{ $payment->paid_at->format('d/m/Y H:i') }}</p>
                                @endif
                            @elseif($payment->status === 'pending')
                                <span class="badge bg-warning fs-6">Aguardando Pagamento</span>
                            @elseif($payment->status === 'overdue')
                                <span class="badge bg-danger fs-6">Vencido</span>
                            @else
                                <span class="badge bg-secondary fs-6">{{ ucfirst($payment->status) }}</span>
                            @endif
                        </div>
                    </div>

                    @if($payment->payment_method === 'pix' && $payment->status === 'pending')
                    <hr>
                    <div class="text-center py-4">
                        <h5 class="fw-bold mb-3">Pagar com PIX</h5>
                        <p class="text-muted mb-4">Escaneie o QR Code abaixo ou copie o código PIX</p>
                        
                        @if($payment->pix_qr_code)
                        <div class="mb-4">
                            <img src="data:image/png;base64,{{ $payment->pix_qr_code }}" alt="QR Code PIX" class="img-fluid" style="max-width: 300px;">
                        </div>
                        @endif

                        @if($payment->pix_payload)
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Código PIX Copia e Cola</label>
                            <div class="input-group">
                                <input type="text" class="form-control text-center" id="pixCode" value="{{ $payment->pix_payload }}" readonly>
                                <button class="btn btn-outline-primary" type="button" onclick="copyPixCode()">
                                    <i class="bi bi-clipboard"></i> Copiar
                                </button>
                            </div>
                        </div>
                        @endif

                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i>
                            O pagamento será confirmado automaticamente após a aprovação do PIX.
                        </div>
                    </div>
                    @endif

                    <div class="d-grid gap-2 mt-4">
                        <a href="{{ route('payments.index') }}" class="btn btn-outline-secondary">
                            Voltar para Pagamentos
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function copyPixCode() {
    const pixCode = document.getElementById('pixCode');
    pixCode.select();
    pixCode.setSelectionRange(0, 99999);
    navigator.clipboard.writeText(pixCode.value);
    
    const btn = event.target.closest('button');
    const originalText = btn.innerHTML;
    btn.innerHTML = '<i class="bi bi-check"></i> Copiado!';
    btn.classList.remove('btn-outline-primary');
    btn.classList.add('btn-success');
    
    setTimeout(() => {
        btn.innerHTML = originalText;
        btn.classList.remove('btn-success');
        btn.classList.add('btn-outline-primary');
    }, 2000);
}
</script>
@endpush
@endsection
