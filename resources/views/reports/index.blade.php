@extends('layouts.base')

@section('title', 'Relatórios')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-start mb-4 flex-wrap gap-3">
        <div>
            <h2 class="fw-bold mb-0">Relatórios</h2>
            <p class="text-muted mb-0 small">Estatísticas e métricas do sistema</p>
        </div>
        <div class="d-flex gap-2">
            <select class="form-select form-select-sm" id="periodFilter" onchange="window.location.href='?period='+this.value">
                <option value="7" {{ $period == 7 ? 'selected' : '' }}>Últimos 7 dias</option>
                <option value="30" {{ $period == 30 ? 'selected' : '' }}>Últimos 30 dias</option>
                <option value="90" {{ $period == 90 ? 'selected' : '' }}>Últimos 90 dias</option>
                <option value="365" {{ $period == 365 ? 'selected' : '' }}>Último ano</option>
            </select>
        </div>
    </div>

    <!-- Cards de Estatísticas Rápidas -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted small">Alunos Ativos</span>
                        <i class="bi bi-people-fill text-primary fs-4"></i>
                    </div>
                    <h3 class="fw-bold mb-0">{{ $activeUsers }}</h3>
                    <small class="text-muted">de {{ $totalUsers }} total</small>
                </div>
            </div>
        </div>

        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted small">Reservas</span>
                        <i class="bi bi-calendar-check text-success fs-4"></i>
                    </div>
                    <h3 class="fw-bold mb-0">{{ $totalBookings }}</h3>
                    <small class="text-success">{{ $attendanceRate }}% compareceram</small>
                </div>
            </div>
        </div>

        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted small">Receita</span>
                        <i class="bi bi-currency-dollar text-warning fs-4"></i>
                    </div>
                    <h3 class="fw-bold mb-0">R$ {{ number_format($totalRevenue / 100, 2, ',', '.') }}</h3>
                    <small class="text-muted">recebido</small>
                </div>
            </div>
        </div>

        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted small">A Receber</span>
                        <i class="bi bi-hourglass-split text-info fs-4"></i>
                    </div>
                    <h3 class="fw-bold mb-0">R$ {{ number_format($pendingRevenue / 100, 2, ',', '.') }}</h3>
                    <small class="text-muted">pendente</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráfico de Reservas por Dia -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white border-0 py-3">
            <h5 class="mb-0"><i class="bi bi-graph-up text-primary me-2"></i>Reservas por Dia (últimos 7 dias)</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Data</th>
                            <th class="text-end">Reservas</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($bookingsByDay as $day)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($day->date)->format('d/m/Y') }}</td>
                            <td class="text-end">
                                <div class="d-flex align-items-center justify-content-end gap-2">
                                    <div class="progress flex-grow-1" style="height: 20px; max-width: 200px;">
                                        <div class="progress-bar bg-primary" role="progressbar" 
                                             style="width: {{ ($day->total / $bookingsByDay->max('total')) * 100 }}%">
                                        </div>
                                    </div>
                                    <span class="badge bg-primary">{{ $day->total }}</span>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="2" class="text-center text-muted py-4">Nenhuma reserva no período</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <!-- Top Alunos -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0"><i class="bi bi-trophy text-warning me-2"></i>Alunos Mais Frequentes</h5>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        @forelse($topStudents as $index => $student)
                        <div class="list-group-item px-0 border-0 d-flex align-items-center gap-3">
                            <div class="badge 
                                @if($index === 0) bg-warning
                                @elseif($index === 1) bg-secondary  
                                @elseif($index === 2) bg-danger
                                @else bg-light text-dark
                                @endif rounded-circle" style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;">
                                {{ $index + 1 }}
                            </div>
                            <div class="flex-grow-1">
                                <div class="fw-semibold">{{ $student->name }}</div>
                                <small class="text-muted">{{ $student->email }}</small>
                            </div>
                            <div class="text-end">
                                <div class="badge bg-primary">{{ $student->bookings_count }} aulas</div>
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-4 text-muted">
                            <i class="bi bi-inbox fs-1"></i>
                            <p class="mb-0 mt-2">Nenhum dado disponível</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- Horários Mais Populares -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0"><i class="bi bi-clock text-success me-2"></i>Horários Mais Populares</h5>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        @forelse($popularSchedules as $schedule)
                        <div class="list-group-item px-0 border-0">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <div class="fw-semibold">{{ $schedule->starts_at->format('d/m/Y') }}</div>
                                    <small class="text-muted">{{ $schedule->starts_at->format('H:i') }} - {{ $schedule->ends_at->format('H:i') }}</small>
                                </div>
                                <span class="badge bg-success">{{ $schedule->bookings_count }} reservas</span>
                            </div>
                            <div class="progress" style="height: 8px;">
                                @php
                                    $capacity = $schedule->capacity_override ?? 10;
                                    $percentage = ($schedule->bookings_count / $capacity) * 100;
                                @endphp
                                <div class="progress-bar bg-success" role="progressbar" 
                                     style="width: {{ min($percentage, 100) }}%">
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-4 text-muted">
                            <i class="bi bi-inbox fs-1"></i>
                            <p class="mb-0 mt-2">Nenhum horário com reservas</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Detalhes de Reservas -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white border-0 py-3">
            <h5 class="mb-0"><i class="bi bi-pie-chart text-info me-2"></i>Status das Reservas</h5>
        </div>
        <div class="card-body">
            <div class="row g-3 text-center">
                <div class="col-4">
                    <div class="border rounded p-3">
                        <i class="bi bi-check-circle-fill text-success fs-1"></i>
                        <h4 class="fw-bold mt-2 mb-0">{{ $attendedBookings }}</h4>
                        <small class="text-muted">Compareceram</small>
                    </div>
                </div>
                <div class="col-4">
                    <div class="border rounded p-3">
                        <i class="bi bi-x-circle-fill text-danger fs-1"></i>
                        <h4 class="fw-bold mt-2 mb-0">{{ $canceledBookings }}</h4>
                        <small class="text-muted">Canceladas</small>
                    </div>
                </div>
                <div class="col-4">
                    <div class="border rounded p-3">
                        <i class="bi bi-clock-fill text-warning fs-1"></i>
                        <h4 class="fw-bold mt-2 mb-0">{{ $totalBookings - $attendedBookings - $canceledBookings }}</h4>
                        <small class="text-muted">Pendentes</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
