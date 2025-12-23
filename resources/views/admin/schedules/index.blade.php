@extends('layouts.base')

@section('title', 'Gerenciar Horários')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-1">Gerenciar Horários</h1>
                    <p class="text-muted mb-0">Configurações de funcionamento e bloqueios</p>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Tabs -->
    <ul class="nav nav-tabs mb-4" id="scheduleTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="schedule-tab" data-bs-toggle="tab" data-bs-target="#schedule" type="button">
                <i class="bi bi-clock me-2"></i>Horários de Funcionamento
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="blocks-tab" data-bs-toggle="tab" data-bs-target="#blocks" type="button">
                <i class="bi bi-calendar-x me-2"></i>Bloqueios
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="settings-tab" data-bs-toggle="tab" data-bs-target="#settings" type="button">
                <i class="bi bi-gear me-2"></i>Configurações
            </button>
        </li>
    </ul>

    <div class="tab-content" id="scheduleTabsContent">
        <!-- Horários de Funcionamento -->
        <div class="tab-pane fade show active" id="schedule" role="tabpanel">
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Horários Padrão da Academia</h5>
                    <small class="text-muted">Estes horários estão sempre disponíveis para agendamento, a menos que sejam bloqueados.</small>
                </div>
                <div class="card-body">
                    @php
                        $operatingHours = json_decode(\App\Models\Setting::get('operating_hours', '{}'), true);
                        $days = [
                            'monday' => 'Segunda-feira',
                            'tuesday' => 'Terça-feira',
                            'wednesday' => 'Quarta-feira',
                            'thursday' => 'Quinta-feira',
                            'friday' => 'Sexta-feira',
                            'saturday' => 'Sábado',
                            'sunday' => 'Domingo',
                        ];
                    @endphp

                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Dia da Semana</th>
                                    <th>Horário</th>
                                    <th>Status</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($days as $key => $dayName)
                                    @php
                                        $dayConfig = $operatingHours[$key] ?? null;
                                    @endphp
                                    <tr>
                                        <td>
                                            <strong>{{ $dayName }}</strong>
                                        </td>
                                        <td>
                                            @if($dayConfig && $dayConfig['enabled'])
                                                <span class="badge bg-light text-dark">
                                                    <i class="bi bi-clock"></i>
                                                    {{ $dayConfig['start'] }} - {{ $dayConfig['end'] }}
                                                </span>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($dayConfig && $dayConfig['enabled'])
                                                <span class="badge bg-success">
                                                    <i class="bi bi-check-circle"></i> Aberto
                                                </span>
                                            @else
                                                <span class="badge bg-secondary">
                                                    <i class="bi bi-x-circle"></i> Fechado
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-primary" disabled>
                                                <i class="bi bi-pencil"></i> Editar
                                            </button>
                                            <small class="text-muted ms-2">(Em breve)</small>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="alert alert-info mt-3">
                        <i class="bi bi-info-circle me-2"></i>
                        <strong>Como funciona:</strong> Os horários acima são gerados automaticamente. 
                        Para bloquear um dia ou horário específico, vá para a aba "Bloqueios".
                    </div>
                </div>
            </div>
        </div>

        <!-- Bloqueios -->
        <div class="tab-pane fade" id="blocks" role="tabpanel">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0">Bloqueios Ativos e Futuros</h5>
                <a href="{{ route('admin.schedule-blocks.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-2"></i>Novo Bloqueio
                </a>
            </div>

            <div class="card">
                <div class="card-body">
                    @if($blocks->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Data</th>
                                        <th>Horário</th>
                                        <th>Motivo</th>
                                        <th>Observações</th>
                                        <th>Criado por</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($blocks as $block)
                                        <tr>
                                            <td>
                                                <strong>{{ \Carbon\Carbon::parse($block->date)->format('d/m/Y') }}</strong>
                                                <br>
                                                <small class="text-muted">{{ \Carbon\Carbon::parse($block->date)->translatedFormat('l') }}</small>
                                            </td>
                                            <td>
                                                <span class="badge bg-light text-dark">
                                                    {{ substr($block->start_time, 0, 5) }} - {{ substr($block->end_time, 0, 5) }}
                                                </span>
                                            </td>
                                            <td>
                                                @php
                                                    $reasonLabels = [
                                                        'maintenance' => ['label' => 'Manutenção', 'class' => 'warning'],
                                                        'holiday' => ['label' => 'Feriado', 'class' => 'info'],
                                                        'event' => ['label' => 'Evento', 'class' => 'primary'],
                                                        'other' => ['label' => 'Outro', 'class' => 'secondary'],
                                                    ];
                                                    $reason = $reasonLabels[$block->reason] ?? $reasonLabels['other'];
                                                @endphp
                                                <span class="badge bg-{{ $reason['class'] }}">
                                                    {{ $reason['label'] }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($block->notes)
                                                    <small>{{ Str::limit($block->notes, 50) }}</small>
                                                @else
                                                    <span class="text-muted">—</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($block->creator)
                                                    <small>{{ $block->creator->name }}</small>
                                                @else
                                                    <span class="text-muted">Sistema</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="{{ route('admin.schedule-blocks.edit', $block) }}" 
                                                       class="btn btn-outline-primary" title="Editar">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                    <form action="{{ route('admin.schedule-blocks.destroy', $block) }}" 
                                                          method="POST" class="d-inline"
                                                          onsubmit="return confirm('Tem certeza que deseja remover este bloqueio?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-outline-danger" title="Remover">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-3">
                            {{ $blocks->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-calendar-check fs-1 text-muted"></i>
                            <p class="text-muted mt-3">Nenhum bloqueio ativo no momento.</p>
                            <p class="text-muted">Todos os horários configurados estão disponíveis para agendamento.</p>
                            <a href="{{ route('admin.schedule-blocks.create') }}" class="btn btn-primary mt-2">
                                <i class="bi bi-plus-circle me-2"></i>Criar Primeiro Bloqueio
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Configurações -->
        <div class="tab-pane fade" id="settings" role="tabpanel">
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Configurações Gerais</h5>
                </div>
                <div class="card-body">
                    @php
                        $slotDuration = \App\Models\Setting::get('slot_duration', 60);
                        $defaultCapacity = \App\Models\Setting::get('max_capacity_per_class', 20);
                    @endphp

                    <div class="row">
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="text-muted mb-2">
                                        <i class="bi bi-hourglass-split me-2"></i>Duração dos Slots
                                    </h6>
                                    <h3 class="mb-0">{{ $slotDuration }} minutos</h3>
                                    <p class="text-muted small mb-0 mt-2">
                                        Cada horário disponível terá esta duração
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="text-muted mb-2">
                                        <i class="bi bi-people me-2"></i>Capacidade Padrão
                                    </h6>
                                    <h3 class="mb-0">{{ $defaultCapacity }} pessoas</h3>
                                    <p class="text-muted small mb-0 mt-2">
                                        Número máximo de alunos por horário
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-warning mt-4">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        <strong>Edição de configurações:</strong> Em breve você poderá alterar essas configurações diretamente por aqui.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
