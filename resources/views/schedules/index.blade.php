@extends('layouts.base')

@section('title', 'Horários')

@section('content')
@extends('layouts.base')

@section('title', 'Gerenciar Horários')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Gerenciar Horários</h1>
        <a href="{{ route('schedules.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Novo Horário
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Data/Hora Início</th>
                            <th>Data/Hora Fim</th>
                            <th>Duração</th>
                            <th>Capacidade</th>
                            <th>Reservas</th>
                            <th>Vagas</th>
                            <th>Status</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($schedules as $schedule)
                            <tr>
                                <td>{{ $schedule->starts_at->format('d/m/Y H:i') }}</td>
                                <td>{{ $schedule->ends_at->format('d/m/Y H:i') }}</td>
                                <td>{{ $schedule->starts_at->diffInMinutes($schedule->ends_at) }}min</td>
                                <td>
                                    @if($schedule->capacity_override)
                                        {{ $schedule->capacity_override }}
                                        <small class="text-muted">(customizado)</small>
                                    @else
                                        {{ \App\Models\Setting::where('key', 'max_capacity_per_class')->value('value') ?? 10 }}
                                        <small class="text-muted">(padrão)</small>
                                    @endif
                                </td>
                                <td>{{ $schedule->bookings()->whereIn('status', ['booked', 'attended'])->count() }}</td>
                                <td>
                                    @php
                                        $spots = $schedule->available_spots;
                                        $capacity = $schedule->capacity_override ?? (\App\Models\Setting::where('key', 'max_capacity_per_class')->value('value') ?? 10);
                                        $percentage = ($capacity > 0) ? ($spots / $capacity) * 100 : 0;
                                    @endphp
                                    <span class="badge 
                                        @if($percentage > 50) bg-success
                                        @elseif($percentage > 20) bg-warning
                                        @else bg-danger
                                        @endif">
                                        {{ $spots }} vagas
                                    </span>
                                </td>
                                <td>
                                    @if($schedule->status === 'open')
                                        <span class="badge bg-success">Aberto</span>
                                    @elseif($schedule->status === 'closed')
                                        <span class="badge bg-secondary">Fechado</span>
                                    @else
                                        <span class="badge bg-warning">Feriado</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('schedules.edit', $schedule) }}" class="btn btn-outline-primary">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="{{ route('schedules.destroy', $schedule) }}" method="POST" 
                                              onsubmit="return confirm('Tem certeza? Isso excluirá todas as reservas deste horário!')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4">
                                    <i class="bi bi-calendar-x fs-1 text-muted"></i>
                                    <p class="text-muted mt-2">Nenhum horário cadastrado ainda.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $schedules->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
@endsection
