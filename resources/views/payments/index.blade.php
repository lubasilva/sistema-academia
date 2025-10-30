@extends('layouts.base')

@section('title', 'Meus Pagamentos')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col">
            <h2 class="fw-bold">Meus Pagamentos</h2>
            <p class="text-muted">Histórico de pagamentos e assinaturas</p>
        </div>
        <div class="col-auto">
            <a href="{{ route('payments.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Novo Plano
            </a>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Data</th>
                            <th>Plano</th>
                            <th>Valor</th>
                            <th>Método</th>
                            <th>Vencimento</th>
                            <th>Status</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($payments as $payment)
                        <tr>
                            <td>{{ $payment->created_at->format('d/m/Y H:i') }}</td>
                            <td>{{ $payment->plan->name }}</td>
                            <td>R$ {{ number_format($payment->amount, 2, ',', '.') }}</td>
                            <td>
                                @if($payment->payment_method === 'pix')
                                    <i class="bi bi-qr-code"></i> PIX
                                @elseif($payment->payment_method === 'boleto')
                                    <i class="bi bi-file-earmark-text"></i> Boleto
                                @else
                                    {{ ucfirst($payment->payment_method) }}
                                @endif
                            </td>
                            <td>{{ $payment->due_date ? $payment->due_date->format('d/m/Y') : '-' }}</td>
                            <td>
                                @if($payment->status === 'paid')
                                    <span class="badge bg-success">Pago</span>
                                @elseif($payment->status === 'pending')
                                    <span class="badge bg-warning">Pendente</span>
                                @elseif($payment->status === 'overdue')
                                    <span class="badge bg-danger">Vencido</span>
                                @elseif($payment->status === 'cancelled')
                                    <span class="badge bg-secondary">Cancelado</span>
                                @else
                                    <span class="badge bg-info">{{ $payment->status }}</span>
                                @endif
                            </td>
                            <td>
                                @if($payment->payment_method === 'pix' && $payment->status === 'pending')
                                    <a href="{{ route('payments.show', $payment) }}" class="btn btn-sm btn-primary">
                                        <i class="bi bi-qr-code"></i> Ver QR Code
                                    </a>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                Nenhum pagamento encontrado
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($payments->hasPages())
            <div class="mt-3">
                {{ $payments->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
