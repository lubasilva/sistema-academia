@extends('layouts.base')

@section('title', 'Detalhes do Bloqueio')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('schedules.index') }}">Horários</a></li>
                    <li class="breadcrumb-item active">Detalhes do Bloqueio</li>
                </ol>
            </nav>
            <h1 class="h3">Detalhes do Bloqueio</h1>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Informações do Bloqueio</h5>
                    <div class="btn-group btn-group-sm">
                        <a href="{{ route('admin.schedule-blocks.edit', $scheduleBlock) }}" class="btn btn-outline-primary">
                            <i class="bi bi-pencil me-1"></i>Editar
                        </a>
                        <form action="{{ route('admin.schedule-blocks.destroy', $scheduleBlock) }}" 
                              method="POST" class="d-inline"
                              onsubmit="return confirm('Tem certeza que deseja remover este bloqueio?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger">
                                <i class="bi bi-trash me-1"></i>Remover
                            </button>
                        </form>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">Data</label>
                            <p class="fs-5 mb-0">
                                <i class="bi bi-calendar3 me-2"></i>
                                {{ $scheduleBlock->date->format('d/m/Y') }}
                            </p>
                            <small class="text-muted">{{ $scheduleBlock->date->translatedFormat('l') }}</small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">Horário</label>
                            <p class="fs-5 mb-0">
                                <i class="bi bi-clock me-2"></i>
                                {{ substr($scheduleBlock->start_time, 0, 5) }} - {{ substr($scheduleBlock->end_time, 0, 5) }}
                            </p>
                            @php
                                $start = \Carbon\Carbon::parse($scheduleBlock->start_time);
                                $end = \Carbon\Carbon::parse($scheduleBlock->end_time);
                                $duration = $start->diffInMinutes($end);
                            @endphp
                            <small class="text-muted">Duração: {{ $duration }} minutos</small>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">Motivo</label>
                            @php
                                $reasonLabels = [
                                    'maintenance' => ['label' => 'Manutenção', 'class' => 'warning', 'icon' => 'tools'],
                                    'holiday' => ['label' => 'Feriado', 'class' => 'info', 'icon' => 'calendar-event'],
                                    'event' => ['label' => 'Evento', 'class' => 'primary', 'icon' => 'calendar-star'],
                                    'other' => ['label' => 'Outro', 'class' => 'secondary', 'icon' => 'three-dots'],
                                ];
                                $reason = $reasonLabels[$scheduleBlock->reason] ?? $reasonLabels['other'];
                            @endphp
                            <p class="mb-0">
                                <span class="badge bg-{{ $reason['class'] }} fs-6">
                                    <i class="bi bi-{{ $reason['icon'] }} me-2"></i>{{ $reason['label'] }}
                                </span>
                            </p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">Status</label>
                            <p class="mb-0">
                                @if($scheduleBlock->date->isPast())
                                    <span class="badge bg-secondary fs-6">
                                        <i class="bi bi-check-circle me-1"></i>Finalizado
                                    </span>
                                @elseif($scheduleBlock->date->isToday())
                                    <span class="badge bg-warning fs-6">
                                        <i class="bi bi-exclamation-circle me-1"></i>Hoje
                                    </span>
                                @else
                                    <span class="badge bg-primary fs-6">
                                        <i class="bi bi-calendar-check me-1"></i>Agendado
                                    </span>
                                @endif
                            </p>
                        </div>
                    </div>

                    @if($scheduleBlock->notes)
                        <div class="mt-3">
                            <label class="text-muted small">Observações</label>
                            <div class="alert alert-light border">
                                {{ $scheduleBlock->notes }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card bg-light">
                <div class="card-body">
                    <h6 class="card-title">
                        <i class="bi bi-info-circle me-2"></i>Metadados
                    </h6>
                    
                    <hr>
                    
                    <div class="mb-3">
                        <small class="text-muted">ID do Bloqueio</small>
                        <p class="mb-0"><code>#{{ $scheduleBlock->id }}</code></p>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted">Criado em</small>
                        <p class="mb-0">{{ $scheduleBlock->created_at->format('d/m/Y H:i:s') }}</p>
                        <small class="text-muted">{{ $scheduleBlock->created_at->diffForHumans() }}</small>
                    </div>

                    @if($scheduleBlock->creator)
                        <div class="mb-3">
                            <small class="text-muted">Criado por</small>
                            <p class="mb-0">
                                <i class="bi bi-person me-1"></i>{{ $scheduleBlock->creator->name }}
                            </p>
                            <small class="text-muted">{{ $scheduleBlock->creator->email }}</small>
                        </div>
                    @endif

                    @if($scheduleBlock->updated_at != $scheduleBlock->created_at)
                        <div class="mb-3">
                            <small class="text-muted">Última atualização</small>
                            <p class="mb-0">{{ $scheduleBlock->updated_at->format('d/m/Y H:i:s') }}</p>
                            <small class="text-muted">{{ $scheduleBlock->updated_at->diffForHumans() }}</small>
                        </div>
                    @endif
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-body">
                    <h6 class="card-title">
                        <i class="bi bi-clock-history me-2"></i>Timeline
                    </h6>
                    
                    <div class="timeline">
                        <div class="timeline-item">
                            <div class="timeline-marker bg-success"></div>
                            <div class="timeline-content">
                                <small class="text-muted">{{ $scheduleBlock->created_at->format('d/m H:i') }}</small>
                                <p class="mb-0 small">Bloqueio criado</p>
                            </div>
                        </div>
                        
                        @if($scheduleBlock->updated_at != $scheduleBlock->created_at)
                            <div class="timeline-item">
                                <div class="timeline-marker bg-primary"></div>
                                <div class="timeline-content">
                                    <small class="text-muted">{{ $scheduleBlock->updated_at->format('d/m H:i') }}</small>
                                    <p class="mb-0 small">Última edição</p>
                                </div>
                            </div>
                        @endif
                        
                        @if($scheduleBlock->date->isFuture())
                            <div class="timeline-item">
                                <div class="timeline-marker bg-warning"></div>
                                <div class="timeline-content">
                                    <small class="text-muted">{{ $scheduleBlock->date->format('d/m') }}</small>
                                    <p class="mb-0 small">Bloqueio ativo</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline-item {
    position: relative;
    padding-bottom: 20px;
}

.timeline-item:not(:last-child):before {
    content: '';
    position: absolute;
    left: -24px;
    top: 12px;
    width: 2px;
    height: 100%;
    background: #e0e0e0;
}

.timeline-marker {
    position: absolute;
    left: -28px;
    top: 2px;
    width: 10px;
    height: 10px;
    border-radius: 50%;
}

.timeline-content {
    padding-left: 0;
}
</style>
@endpush
@endsection
