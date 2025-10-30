@extends('layouts.base')

@section('title', 'Dashboard')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col">
            <h2 class="fw-bold">Dashboard</h2>
            <p class="text-muted">Bem-vindo(a), {{ auth()->user()->name }}!</p>
        </div>
    </div>

    @if(auth()->user()->role === 'admin')
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="card card-stats border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted text-uppercase small mb-1">Total de Alunos</h6>
                                <h3 class="fw-bold mb-0">{{ $stats['total_students'] ?? 0 }}</h3>
                            </div>
                            <div class="text-primary fs-2">
                                <i class="bi bi-people"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card border-0 shadow-sm" style="border-left: 4px solid #28a745 !important;">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted text-uppercase small mb-1">Reservas Hoje</h6>
                                <h3 class="fw-bold mb-0">{{ $stats['bookings_today'] ?? 0 }}</h3>
                            </div>
                            <div class="text-success fs-2">
                                <i class="bi bi-calendar-check"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card border-0 shadow-sm" style="border-left: 4px solid #ffc107 !important;">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted text-uppercase small mb-1">Instrutores</h6>
                                <h3 class="fw-bold mb-0">{{ $stats['total_instructors'] ?? 0 }}</h3>
                            </div>
                            <div class="text-warning fs-2">
                                <i class="bi bi-person-badge"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card border-0 shadow-sm" style="border-left: 4px solid #dc3545 !important;">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted text-uppercase small mb-1">Receita Mês</h6>
                                <h3 class="fw-bold mb-0">R$ {{ number_format($stats['revenue_month'] ?? 0, 2, ',', '.') }}</h3>
                            </div>
                            <div class="text-danger fs-2">
                                <i class="bi bi-cash-stack"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gráficos -->
        <div class="row g-3 mb-4">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-0 pt-3">
                        <h5 class="fw-semibold mb-0">Reservas (Últimos 7 Dias)</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="bookingsChart" height="80"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-0 pt-3">
                        <h5 class="fw-semibold mb-0">Receita (6 Meses)</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="revenueChart" height="160"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-md-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-0 pt-3">
                        <h5 class="fw-semibold mb-0">Reservas Recentes</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Aluno</th>
                                        <th>Data</th>
                                        <th>Horário</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach(\App\Models\Booking::with('user', 'schedule')->latest()->take(10)->get() as $booking)
                                    <tr>
                                        <td>{{ $booking->user->name }}</td>
                                        <td>{{ $booking->schedule->starts_at->format('d/m/Y') }}</td>
                                        <td>{{ $booking->schedule->starts_at->format('H:i') }}</td>
                                        <td>
                                            @if($booking->status === 'confirmed')
                                                <span class="badge bg-success">Confirmado</span>
                                            @elseif($booking->status === 'cancelled')
                                                <span class="badge bg-danger">Cancelado</span>
                                            @else
                                                <span class="badge bg-warning">Pendente</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-0 pt-3">
                        <h5 class="fw-semibold mb-0">Atividades Recentes</h5>
                    </div>
                    <div class="card-body">
                        <div class="list-group list-group-flush">
                            @foreach(\App\Models\AuditLog::with('user')->latest('created_at')->take(10)->get() as $log)
                            <div class="list-group-item border-0 px-0">
                                <div class="d-flex align-items-start">
                                    <div class="flex-shrink-0">
                                        <i class="bi bi-circle-fill text-primary" style="font-size: 0.5rem;"></i>
                                    </div>
                                    <div class="flex-grow-1 ms-2">
                                        <div class="small">
                                            <strong>{{ $log->user?->name ?? 'Sistema' }}</strong>
                                        </div>
                                        <div class="small text-muted">{{ $log->description ?? $log->action }}</div>
                                        <div class="small text-muted">{{ \Carbon\Carbon::parse($log->created_at)->diffForHumans() }}</div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @push('scripts')
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Gráfico de Reservas
            const bookingsData = @json($bookingsByDay ?? []);
            const bookingsCtx = document.getElementById('bookingsChart').getContext('2d');
            new Chart(bookingsCtx, {
                type: 'line',
                data: {
                    labels: bookingsData.map(item => item.date),
                    datasets: [{
                        label: 'Reservas',
                        data: bookingsData.map(item => item.count),
                        borderColor: '#007bff',
                        backgroundColor: 'rgba(0, 123, 255, 0.1)',
                        tension: 0.4,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1
                            }
                        }
                    }
                }
            });

            // Gráfico de Receita
            const revenueData = @json($revenueByMonth ?? []);
            const revenueCtx = document.getElementById('revenueChart').getContext('2d');
            new Chart(revenueCtx, {
                type: 'bar',
                data: {
                    labels: revenueData.map(item => {
                        const month = item.month;
                        const year = item.year;
                        return new Date(year, month - 1).toLocaleDateString('pt-BR', { month: 'short' });
                    }),
                    datasets: [{
                        label: 'Receita (R$)',
                        data: revenueData.map(item => item.total),
                        backgroundColor: '#28a745',
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        });
        </script>
        @endpush

    @elseif(auth()->user()->role === 'instructor')
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h6 class="text-muted text-uppercase small mb-1">Aulas Hoje</h6>
                        <h3 class="fw-bold mb-0">{{ $stats['classes_today'] ?? 0 }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h6 class="text-muted text-uppercase small mb-1">Aulas Esta Semana</h6>
                        <h3 class="fw-bold mb-0">{{ $stats['classes_week'] ?? 0 }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h6 class="text-muted text-uppercase small mb-1">Total Alunos</h6>
                        <h3 class="fw-bold mb-0">{{ $stats['total_students'] ?? 0 }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-md-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-0 pt-3">
                        <h5 class="fw-semibold mb-0">Próximas Aulas</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Data</th>
                                        <th>Horário</th>
                                        <th>Aluno</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($upcomingClasses ?? [] as $class)
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($class->date)->format('d/m/Y') }}</td>
                                        <td>{{ \Carbon\Carbon::parse($class->schedule->time)->format('H:i') }}</td>
                                        <td>{{ $class->user->name }}</td>
                                        <td><span class="badge bg-success">Confirmado</span></td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted">Nenhuma aula agendada</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    @else
        <div class="row g-3 mb-4">
            @if($stats['active_plan'] ?? null)
            <div class="col-md-4">
                <div class="card border-0 shadow-sm border-primary border-2">
                    <div class="card-body">
                        <h6 class="text-muted text-uppercase small mb-2">Meu Plano</h6>
                        <h4 class="fw-bold text-primary mb-2">{{ $stats['active_plan']->plan->name }}</h4>
                        <p class="mb-1"><strong>Créditos:</strong> {{ $stats['active_plan']->credits_remaining }}</p>
                        <p class="mb-0 small text-muted">Válido até: {{ \Carbon\Carbon::parse($stats['active_plan']->ends_at)->format('d/m/Y') }}</p>
                    </div>
                </div>
            </div>
            @endif

            <div class="col-md-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h6 class="text-muted text-uppercase small mb-1">Próximas Reservas</h6>
                        <h3 class="fw-bold mb-0">{{ $stats['bookings_upcoming'] ?? 0 }}</h3>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h6 class="text-muted text-uppercase small mb-1">Total de Reservas</h6>
                        <h3 class="fw-bold mb-0">{{ $stats['bookings_total'] ?? 0 }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-md-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-0 pt-3">
                        <h5 class="fw-semibold mb-0">Minhas Próximas Reservas</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Data</th>
                                        <th>Horário</th>
                                        <th>Status</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($upcomingBookings ?? [] as $booking)
                                    <tr>
                                        <td>{{ $booking->schedule->starts_at->format('d/m/Y') }}</td>
                                        <td>{{ $booking->schedule->starts_at->format('H:i') }}</td>
                                        <td><span class="badge bg-success">Confirmado</span></td>
                                        <td>
                                            <form action="{{ route('bookings.destroy', $booking) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Deseja cancelar esta reserva?')">
                                                    Cancelar
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-4">
                                            Nenhuma reserva agendada
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-header bg-white border-0 pt-3">
                        <h5 class="fw-semibold mb-0">Ações Rápidas</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="{{ route('bookings.index') }}" class="btn btn-primary">
                                <i class="bi bi-calendar-plus"></i> Nova Reserva
                            </a>
                            <a href="{{ route('payments.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-credit-card"></i> Meus Pagamentos
                            </a>
                            @if(!($stats['active_plan'] ?? null))
                            <a href="{{ route('payments.create') }}" class="btn btn-outline-success">
                                <i class="bi bi-cart-plus"></i> Assinar Plano
                            </a>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-0 pt-3">
                        <h5 class="fw-semibold mb-0">Pagamentos Recentes</h5>
                    </div>
                    <div class="card-body">
                        <div class="list-group list-group-flush">
                            @forelse($recentPayments ?? [] as $payment)
                            <div class="list-group-item border-0 px-0">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="small fw-semibold">{{ $payment->plan->name }}</div>
                                        <div class="small text-muted">{{ $payment->created_at->format('d/m/Y') }}</div>
                                    </div>
                                    <div>
                                        @if($payment->status === 'paid')
                                            <span class="badge bg-success">Pago</span>
                                        @else
                                            <span class="badge bg-warning">Pendente</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @empty
                            <p class="text-muted small mb-0">Nenhum pagamento encontrado</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
