@extends('layouts.base')

@section('title', 'Detalhes do Aluno')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col">
            <div class="d-flex justify-content-between align-items-center">
                <h2 class="fw-bold mb-0">👤 {{ $student->name }}</h2>
                <a href="{{ route('admin.students.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Voltar
                </a>
            </div>
        </div>
    </div>
    
    <!-- Mensagens de sucesso/erro -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Cards de estatísticas -->
    <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card border-primary h-100">
                        <div class="card-body text-center">
                            <h2 class="text-primary mb-0">{{ $stats['total_credits'] }}</h2>
                            <p class="text-muted mb-2">Créditos Disponíveis</p>
                            <small class="text-muted">
                                {{ $stats['regular_credits'] }} regular
                                @if($stats['extra_credits'] > 0)
                                    <br>+ {{ $stats['extra_credits'] }} extra
                                @endif
                            </small>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="card border-success h-100">
                        <div class="card-body text-center">
                            <h2 class="text-success mb-0">{{ $stats['attendance_rate'] }}%</h2>
                            <p class="text-muted mb-2">Taxa de Presença</p>
                            <small class="text-muted">
                                {{ $stats['attended'] }} de {{ $stats['total_bookings'] }} aulas
                            </small>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="card border-warning h-100">
                        <div class="card-body text-center">
                            <h2 class="text-warning mb-0">{{ $stats['absent'] }}</h2>
                            <p class="text-muted mb-2">Faltas</p>
                            <small class="text-muted">
                                {{ $stats['cancelled'] }} cancelamentos
                            </small>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="card border-info h-100">
                        <div class="card-body text-center">
                            <h2 class="text-info mb-0">{{ $stats['used_credits'] }}</h2>
                            <p class="text-muted mb-2">Créditos Usados</p>
                            <small class="text-muted">
                                {{ $stats['booked'] }} agendadas
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Coluna esquerda -->
            <div class="col-lg-8">
                <!-- Ações Rápidas -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-gradient" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                        <h5 class="mb-0">⚡ Ações Rápidas</h5>
                    </div>
                    <div class="card-body">
                        <div class="btn-group-vertical d-grid gap-2 d-md-block" role="group">
                            <!-- Ações principais -->
                            <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#workoutModal">
                                <i class="bi bi-lightning-charge"></i> Gerenciar Treinos
                            </button>
                            <button class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#bioimpedanceModal">
                                <i class="bi bi-activity"></i> Bioimpedância
                            </button>
                            
                            <!-- Ações secundárias - só se tiver plano ativo -->
                            @if($student->activePlan)
                                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addExtraCreditsModal">
                                    <i class="bi bi-plus-circle"></i> Adicionar Créditos Extra
                                </button>
                            @endif
                            <button class="btn btn-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#addObservationModal">
                                <i class="bi bi-chat-left-text"></i> Adicionar Observação
                            </button>
                        </div>
                    </div>
                </div>

                    <!-- Informações do plano -->
                    @if($student->activePlan)
                        <div class="card shadow-sm mb-4">
                            <div class="card-header bg-success text-white">
                                <h5 class="mb-0">📋 Plano Ativo</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <p><strong>Plano:</strong> {{ $student->activePlan->plan->name }}</p>
                                        <p><strong>Início:</strong> {{ \Carbon\Carbon::parse($student->activePlan->start_date)->format('d/m/Y') }}</p>
                                        <p><strong>Término:</strong> {{ \Carbon\Carbon::parse($student->activePlan->end_date)->format('d/m/Y') }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <p><strong>Créditos Regulares:</strong> {{ $stats['regular_credits'] }}</p>
                                        <p><strong>Créditos Extra:</strong> {{ $stats['extra_credits'] }}</p>
                                        <p><strong>Total Usado:</strong> {{ $stats['used_credits'] }}</p>
                                    </div>
                                </div>
                                
                                @if($student->activePlan->observations)
                                    <div class="mt-3">
                                        <strong>Observações:</strong>
                                        <div class="border rounded p-3 bg-light mt-2" style="white-space: pre-wrap;">{{ $student->activePlan->observations }}</div>
                                    </div>
                                @endif
                                
                                <!-- Botões de ação movidos para seção de Ações Rápidas acima -->
                            </div>
                        </div>
                    @else
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle"></i> Este aluno não possui plano ativo.
                        </div>
                    @endif

                    <!-- Histórico de agendamentos -->
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">📅 Histórico de Agendamentos</h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Data</th>
                                            <th>Horário</th>
                                            <th>Status</th>
                                            <th>Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($bookings as $booking)
                                            <tr>
                                                <td>{{ \Carbon\Carbon::parse($booking->date)->format('d/m/Y') }}</td>
                                                <td>
                                                    @if($booking->time)
                                                        {{ \Carbon\Carbon::parse($booking->time)->format('H:i') }}
                                                    @elseif($booking->schedule)
                                                        {{ \Carbon\Carbon::parse($booking->schedule->starts_at)->format('H:i') }}
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($booking->status === 'attended')
                                                        <span class="badge bg-success">Presente</span>
                                                    @elseif($booking->status === 'absent')
                                                        <span class="badge bg-danger">Ausente</span>
                                                    @elseif($booking->status === 'cancelled')
                                                        <span class="badge bg-secondary">Cancelado</span>
                                                    @else
                                                        <span class="badge bg-primary">Agendado</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($booking->status === 'booked' && \Carbon\Carbon::parse($booking->date)->isPast())
                                                        <form method="POST" action="{{ route('admin.bookings.mark-attendance', $booking) }}" class="d-inline">
                                                            @csrf
                                                            <input type="hidden" name="status" value="attended">
                                                            <button type="submit" class="btn btn-sm btn-success" title="Marcar presença">
                                                                <i class="bi bi-check-circle"></i>
                                                            </button>
                                                        </form>
                                                        <form method="POST" action="{{ route('admin.bookings.mark-attendance', $booking) }}" class="d-inline">
                                                            @csrf
                                                            <input type="hidden" name="status" value="absent">
                                                            <button type="submit" class="btn btn-sm btn-danger" title="Marcar falta">
                                                                <i class="bi bi-x-circle"></i>
                                                            </button>
                                                        </form>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center py-3">
                                                    <p class="text-muted mb-0">Nenhum agendamento encontrado.</p>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        @if($bookings->hasPages())
                            <div class="card-footer">
                                {{ $bookings->links('pagination::bootstrap-5') }}
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Coluna direita -->
                <div class="col-lg-4">
                    <!-- Próximas aulas -->
                    @if($stats['upcoming_classes']->count() > 0)
                        <div class="card shadow-sm mb-4">
                            <div class="card-header bg-info text-white">
                                <h5 class="mb-0">🔜 Próximas Aulas</h5>
                            </div>
                            <div class="list-group list-group-flush">
                                @foreach($stats['upcoming_classes'] as $upcoming)
                                    <div class="list-group-item">
                                        <div class="d-flex justify-content-between">
                                            <strong>{{ \Carbon\Carbon::parse($upcoming->date)->format('d/m/Y') }}</strong>
                                            <span class="badge bg-primary">
                                                @if($upcoming->time)
                                                    {{ \Carbon\Carbon::parse($upcoming->time)->format('H:i') }}
                                                @else
                                                    -
                                                @endif
                                            </span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Histórico de créditos -->
                    <div class="card shadow-sm">
                        <div class="card-header bg-warning">
                            <h5 class="mb-0">💳 Histórico de Créditos</h5>
                        </div>
                        <div class="card-body p-0" style="max-height: 500px; overflow-y: auto;">
                            <div class="list-group list-group-flush">
                                @forelse($creditLogs->take(20) as $log)
                                    <div class="list-group-item">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div class="flex-grow-1">
                                                <div class="mb-1">
                                                    @if($log->action_type === 'credit_added')
                                                        <span class="badge bg-success">+ {{ $log->amount }}</span>
                                                        <small>Crédito adicionado</small>
                                                    @elseif($log->action_type === 'extra_credit_added')
                                                        <span class="badge bg-info">+ {{ $log->amount }}</span>
                                                        <small>Crédito extra</small>
                                                    @elseif($log->action_type === 'credit_used')
                                                        <span class="badge bg-danger">- {{ $log->amount }}</span>
                                                        <small>Usado</small>
                                                    @elseif($log->action_type === 'credit_returned')
                                                        <span class="badge bg-warning">+ {{ $log->amount }}</span>
                                                        <small>Devolvido</small>
                                                    @endif
                                                </div>
                                                @if($log->reason)
                                                    <small class="text-muted d-block">{{ $log->reason }}</small>
                                                @endif
                                                <small class="text-muted">
                                                    {{ $log->created_at->format('d/m/Y H:i') }}
                                                    @if($log->creator)
                                                        • por {{ $log->creator->name }}
                                                    @endif
                                                </small>
                                            </div>
                                            <div class="text-end">
                                                <small class="text-muted">Saldo: {{ $log->balance_after }}</small>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="list-group-item text-center">
                                        <p class="text-muted mb-0">Nenhum registro encontrado.</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal: Adicionar Créditos Extra -->
    <div class="modal fade" id="addExtraCreditsModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="{{ route('admin.students.add-extra-credits', $student) }}">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Adicionar Créditos Extra</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="amount" class="form-label">Quantidade de Créditos *</label>
                            <input type="number" 
                                   class="form-control @error('amount') is-invalid @enderror" 
                                   id="amount" 
                                   name="amount" 
                                   min="1" 
                                   max="100" 
                                   required>
                            @error('amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="reason" class="form-label">Motivo *</label>
                            <textarea class="form-control @error('reason') is-invalid @enderror" 
                                      id="reason" 
                                      name="reason" 
                                      rows="3" 
                                      placeholder="Ex: Aula de reposição por falta justificada"
                                      required></textarea>
                            @error('reason')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">
                                Os créditos extra não têm limite semanal e podem ser usados a qualquer momento.
                            </small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-plus-circle"></i> Adicionar Créditos
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal: Adicionar Observação -->
    <div class="modal fade" id="addObservationModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="{{ route('admin.students.add-observation', $student) }}">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Adicionar Observação</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="observation" class="form-label">Observação *</label>
                            <textarea class="form-control @error('observation') is-invalid @enderror" 
                                      id="observation" 
                                      name="observation" 
                                      rows="4" 
                                      placeholder="Digite a observação sobre o aluno..."
                                      required></textarea>
                            @error('observation')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">
                                A observação será registrada com data, hora e seu nome automaticamente.
                            </small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-chat-left-text"></i> Adicionar Observação
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Gerenciar Treinos -->
<div class="modal fade" id="workoutModal" tabindex="-1" aria-labelledby="workoutModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="workoutModalLabel">
                    <i class="bi bi-lightning-charge"></i> Treinos - {{ $student->name }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Tabs para diferentes seções -->
                <ul class="nav nav-tabs" id="workoutTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="workouts-tab" data-bs-toggle="tab" data-bs-target="#workouts" type="button" role="tab">
                            <i class="bi bi-list"></i> Treinos Atuais
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="new-workout-tab" data-bs-toggle="tab" data-bs-target="#new-workout" type="button" role="tab">
                            <i class="bi bi-plus"></i> Novo Treino
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="executions-tab" data-bs-toggle="tab" data-bs-target="#executions" type="button" role="tab">
                            <i class="bi bi-graph-up"></i> Execuções
                        </button>
                    </li>
                </ul>
                
                <div class="tab-content mt-3" id="workoutTabsContent">
                    <!-- Aba: Treinos Atuais -->
                    <div class="tab-pane fade show active" id="workouts" role="tabpanel">
                        @if($student->workoutsAsStudent->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Nome</th>
                                            <th>Instrutor</th>
                                            <th>Criado em</th>
                                            <th>Status</th>
                                            <th class="text-center">Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($student->workoutsAsStudent as $workout)
                                        <tr>
                                            <td>
                                                <strong>{{ $workout->name }}</strong>
                                                @if($workout->description)
                                                    <br><small class="text-muted">{{ Str::limit($workout->description, 50) }}</small>
                                                @endif
                                            </td>
                                            <td>{{ $workout->instructor->name }}</td>
                                            <td>{{ $workout->created_at->format('d/m/Y') }}</td>
                                            <td>
                                                <span class="badge bg-{{ $workout->is_active ? 'success' : 'secondary' }}">
                                                    {{ $workout->is_active ? 'Ativo' : 'Inativo' }}
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <a href="{{ route('workouts.show', $workout) }}" 
                                                   class="btn btn-sm btn-outline-primary" 
                                                   target="_blank"
                                                   title="Ver treino (nova aba)">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                @can('update', $workout)
                                                    <a href="{{ route('workouts.edit', $workout) }}" 
                                                       class="btn btn-sm btn-outline-success" 
                                                       target="_blank"
                                                       title="Editar treino (nova aba)">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                @endcan
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="bi bi-lightning-charge fs-1 text-muted"></i>
                                <p class="text-muted mt-2">Nenhum treino encontrado para este aluno.</p>
                                <p class="text-muted">Clique na aba "Novo Treino" para criar o primeiro treino.</p>
                            </div>
                        @endif
                    </div>
                    
                    <!-- Aba: Novo Treino -->
                    <div class="tab-pane fade" id="new-workout" role="tabpanel">
                        <form action="{{ route('workouts.store.simple') }}" method="POST" id="workoutForm">
                            @csrf
                            <input type="hidden" name="student_id" value="{{ $student->id }}">
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="workout_name" class="form-label">Nome do Treino *</label>
                                        <input type="text" class="form-control" id="workout_name" name="name" required>
                                        <div class="form-text">Ex: Treino A - Peito e Tríceps</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="workout_goal" class="form-label">Objetivo</label>
                                        <select class="form-select" id="workout_goal" name="goal">
                                            <option value="hipertrofia">Hipertrofia</option>
                                            <option value="forca">Força</option>
                                            <option value="resistencia">Resistência</option>
                                            <option value="perda_peso">Perda de Peso</option>
                                            <option value="condicionamento">Condicionamento</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="workout_description" class="form-label">Descrição</label>
                                <textarea class="form-control" id="workout_description" name="description" rows="2"></textarea>
                            </div>
                            
                            <!-- Seção de Exercícios -->
                            <div class="mb-4">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h6 class="mb-0 fw-bold">💪 Exercícios</h6>
                                    <button type="button" class="btn btn-sm btn-outline-success" id="addExerciseBtn">
                                        <i class="bi bi-plus"></i> Adicionar Exercício
                                    </button>
                                </div>
                                
                                <div id="exercisesList" class="border rounded p-3 bg-light min-height-100">
                                    <div class="text-center text-muted py-3" id="noExercisesMsg">
                                        <i class="bi bi-info-circle"></i>
                                        Clique em "Adicionar Exercício" para começar a montar o treino
                                    </div>
                                </div>
                            </div>
                            
                            <div class="d-grid">
                                <button type="submit" class="btn btn-success">
                                    <i class="bi bi-plus"></i> Criar Treino
                                </button>
                            </div>
                        </form>
                        
                        <!-- JavaScript para dinâmica dos exercícios -->
                        <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            let exerciseCount = 0;
                            const exercisesList = document.getElementById('exercisesList');
                            const noExercisesMsg = document.getElementById('noExercisesMsg');
                            const addExerciseBtn = document.getElementById('addExerciseBtn');
                            
                            // Lista de exercícios comuns (pode ser expandida)
                            const commonExercises = [
                                'Supino Reto', 'Supino Inclinado', 'Supino Declinado',
                                'Crucifixo', 'Peck Deck', 'Crossover',
                                'Remada Curvada', 'Remada Sentada', 'Pulldown',
                                'Agachamento', 'Leg Press', 'Cadeira Extensora',
                                'Mesa Flexora', 'Panturrilha em Pé', 'Panturrilha Sentado',
                                'Desenvolvimento', 'Elevação Lateral', 'Elevação Frontal',
                                'Rosca Direta', 'Rosca Martelo', 'Rosca Scott',
                                'Tríceps Testa', 'Tríceps Pulley', 'Mergulho'
                            ];
                            
                            addExerciseBtn.addEventListener('click', function() {
                                exerciseCount++;
                                hideNoExercisesMsg();
                                
                                const exerciseCard = document.createElement('div');
                                exerciseCard.className = 'card mb-3 exercise-card';
                                exerciseCard.dataset.exerciseId = exerciseCount;
                                
                                exerciseCard.innerHTML = `
                                    <div class="card-body p-3">
                                        <div class="d-flex justify-content-between align-items-start mb-3">
                                            <h6 class="mb-0 text-primary">Exercício ${exerciseCount}</h6>
                                            <button type="button" class="btn btn-sm btn-outline-danger remove-exercise">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                        
                                        <div class="row g-2">
                                            <div class="col-12">
                                                <label class="form-label small fw-semibold">Nome do Exercício</label>
                                                <input type="text" class="form-control form-control-sm" 
                                                       name="exercises[${exerciseCount}][name]" 
                                                       placeholder="Ex: Supino Reto"
                                                       list="exercisesSuggestions" required>
                                            </div>
                                            
                                            <div class="col-6 col-md-3">
                                                <label class="form-label small fw-semibold">Séries</label>
                                                <input type="number" class="form-control form-control-sm" 
                                                       name="exercises[${exerciseCount}][sets]" 
                                                       min="1" max="10" value="3" required>
                                            </div>
                                            
                                            <div class="col-6 col-md-3">
                                                <label class="form-label small fw-semibold">Repetições</label>
                                                <input type="text" class="form-control form-control-sm" 
                                                       name="exercises[${exerciseCount}][reps]" 
                                                       placeholder="Ex: 8-12" required>
                                            </div>
                                            
                                            <div class="col-6 col-md-3">
                                                <label class="form-label small fw-semibold">Peso (kg)</label>
                                                <input type="number" class="form-control form-control-sm" 
                                                       name="exercises[${exerciseCount}][weight]" 
                                                       step="0.5" min="0" placeholder="0">
                                            </div>
                                            
                                            <div class="col-6 col-md-3">
                                                <label class="form-label small fw-semibold">Descanso (seg)</label>
                                                <input type="number" class="form-control form-control-sm" 
                                                       name="exercises[${exerciseCount}][rest]" 
                                                       min="10" value="60" required>
                                            </div>
                                            
                                            <div class="col-12">
                                                <label class="form-label small fw-semibold">Observações</label>
                                                <input type="text" class="form-control form-control-sm" 
                                                       name="exercises[${exerciseCount}][notes]" 
                                                       placeholder="Ex: Movimento controlado, foco na contração">
                                            </div>
                                        </div>
                                    </div>
                                `;
                                
                                exercisesList.appendChild(exerciseCard);
                                
                                // Adicionar evento de remoção
                                exerciseCard.querySelector('.remove-exercise').addEventListener('click', function() {
                                    exerciseCard.remove();
                                    checkIfEmpty();
                                });
                                
                                // Foco no campo nome
                                exerciseCard.querySelector('input[name*="[name]"]').focus();
                            });
                            
                            function hideNoExercisesMsg() {
                                if (noExercisesMsg) {
                                    noExercisesMsg.style.display = 'none';
                                }
                            }
                            
                            function checkIfEmpty() {
                                const exerciseCards = exercisesList.querySelectorAll('.exercise-card');
                                if (exerciseCards.length === 0 && noExercisesMsg) {
                                    noExercisesMsg.style.display = 'block';
                                }
                            }
                            
                            // Criar datalist para sugestões
                            const datalist = document.createElement('datalist');
                            datalist.id = 'exercisesSuggestions';
                            commonExercises.forEach(exercise => {
                                const option = document.createElement('option');
                                option.value = exercise;
                                datalist.appendChild(option);
                            });
                            document.body.appendChild(datalist);
                        });
                        </script>
                        
                        <style>
                        .min-height-100 {
                            min-height: 100px;
                        }
                        .exercise-card {
                            border-left: 4px solid #0d6efd;
                        }
                        .exercise-card:nth-child(2n) {
                            border-left-color: #198754;
                        }
                        .exercise-card:nth-child(3n) {
                            border-left-color: #dc3545;
                        }
                        .exercise-card:nth-child(4n) {
                            border-left-color: #fd7e14;
                        }
                        </style>
                    </div>
                    
                    <!-- Aba: Execuções -->
                    <div class="tab-pane fade" id="executions" role="tabpanel">
                        <div class="text-center py-4">
                            <i class="bi bi-graph-up fs-1 text-muted"></i>
                            <p class="text-muted mt-2">Sistema de execuções em desenvolvimento.</p>
                            <p class="text-muted">Em breve você poderá acompanhar o progresso dos treinos aqui.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Bioimpedância -->
<div class="modal fade" id="bioimpedanceModal" tabindex="-1" aria-labelledby="bioimpedanceModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="bioimpedanceModalLabel">
                    <i class="bi bi-activity"></i> Bioimpedância - {{ $student->name }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Tabs para bioimpedância -->
                <ul class="nav nav-tabs" id="bioTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="bio-history-tab" data-bs-toggle="tab" data-bs-target="#bio-history" type="button" role="tab">
                            <i class="bi bi-clock-history"></i> Histórico
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="bio-new-tab" data-bs-toggle="tab" data-bs-target="#bio-new" type="button" role="tab">
                            <i class="bi bi-plus"></i> Nova Medição
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="bio-chart-tab" data-bs-toggle="tab" data-bs-target="#bio-chart" type="button" role="tab">
                            <i class="bi bi-graph-up"></i> Gráficos
                        </button>
                    </li>
                </ul>
                
                <div class="tab-content mt-3" id="bioTabsContent">
                    <!-- Aba: Histórico -->
                    <div class="tab-pane fade show active" id="bio-history" role="tabpanel">
                        @if($student->bioimpedanceMeasurements->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Data</th>
                                            <th>Peso</th>
                                            <th>IMC</th>
                                            <th>% Gordura</th>
                                            <th>Massa Muscular</th>
                                            <th>% Água</th>
                                            <th>Medido por</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($student->bioimpedanceMeasurements->take(10) as $measurement)
                                        <tr>
                                            <td>{{ $measurement->measurement_date->format('d/m/Y') }}</td>
                                            <td>{{ $measurement->weight }}kg</td>
                                            <td>
                                                {{ $measurement->bmi }}
                                                <br><small class="text-muted">{{ $measurement->getBMICategory() }}</small>
                                            </td>
                                            <td>
                                                {{ $measurement->body_fat_percentage }}%
                                                @if($measurement->body_fat_percentage)
                                                    <br><small class="text-muted">{{ $measurement->getBodyFatCategory() }}</small>
                                                @endif
                                            </td>
                                            <td>{{ $measurement->muscle_mass }}kg</td>
                                            <td>{{ $measurement->water_percentage }}%</td>
                                            <td>{{ $measurement->measuredBy->name }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="bi bi-activity fs-1 text-muted"></i>
                                <p class="text-muted mt-2">Nenhuma medição de bioimpedância encontrada.</p>
                                <p class="text-muted">Clique na aba "Nova Medição" para registrar a primeira medição.</p>
                            </div>
                        @endif
                    </div>
                    
                    <!-- Aba: Nova Medição -->
                    <div class="tab-pane fade" id="bio-new" role="tabpanel">
                        <form action="{{ route('admin.students.bioimpedance.store', $student) }}" method="POST">
                            @csrf
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <h6 class="fw-bold mb-3">📏 Medidas Básicas</h6>
                                    
                                    <div class="mb-3">
                                        <label for="bio_weight" class="form-label">Peso (kg) *</label>
                                        <input type="number" step="0.1" class="form-control" id="bio_weight" name="weight" required>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="bio_height" class="form-label">Altura (cm)</label>
                                        <input type="number" step="0.1" class="form-control" id="bio_height" name="height">
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="bio_date" class="form-label">Data da Medição *</label>
                                        <input type="date" class="form-control" id="bio_date" name="measurement_date" value="{{ date('Y-m-d') }}" required>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <h6 class="fw-bold mb-3">🧬 Composição Corporal</h6>
                                    
                                    <div class="mb-3">
                                        <label for="bio_fat" class="form-label">% Gordura Corporal</label>
                                        <input type="number" step="0.1" class="form-control" id="bio_fat" name="body_fat_percentage">
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="bio_muscle" class="form-label">Massa Muscular (kg)</label>
                                        <input type="number" step="0.1" class="form-control" id="bio_muscle" name="muscle_mass">
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="bio_water" class="form-label">% Água Corporal</label>
                                        <input type="number" step="0.1" class="form-control" id="bio_water" name="water_percentage">
                                    </div>
                                </div>
                            </div>
                            
                            <hr>
                            
                            <div class="row">
                                <div class="col-md-4">
                                    <h6 class="fw-bold mb-3">🔥 Metabolismo</h6>
                                    
                                    <div class="mb-3">
                                        <label for="bio_bmr" class="form-label">Taxa Metabólica Basal</label>
                                        <input type="number" class="form-control" id="bio_bmr" name="basal_metabolic_rate">
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="bio_visceral" class="form-label">Gordura Visceral</label>
                                        <input type="number" step="0.1" class="form-control" id="bio_visceral" name="visceral_fat">
                                    </div>
                                </div>
                                
                                <div class="col-md-8">
                                    <h6 class="fw-bold mb-3">📐 Medidas Corporais (cm)</h6>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="bio_chest" class="form-label">Peito/Busto</label>
                                                <input type="number" step="0.1" class="form-control" id="bio_chest" name="chest">
                                            </div>
                                            
                                            <div class="mb-3">
                                                <label for="bio_waist" class="form-label">Cintura</label>
                                                <input type="number" step="0.1" class="form-control" id="bio_waist" name="waist">
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="bio_hip" class="form-label">Quadril</label>
                                                <input type="number" step="0.1" class="form-control" id="bio_hip" name="hip">
                                            </div>
                                            
                                            <div class="mb-3">
                                                <label for="bio_arm" class="form-label">Braço</label>
                                                <input type="number" step="0.1" class="form-control" id="bio_arm" name="arm">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="bio_notes" class="form-label">Observações</label>
                                <textarea class="form-control" id="bio_notes" name="notes" rows="3"></textarea>
                            </div>
                            
                            <div class="d-grid">
                                <button type="submit" class="btn btn-info">
                                    <i class="bi bi-plus"></i> Registrar Medição
                                </button>
                            </div>
                        </form>
                    </div>
                    
                    <!-- Aba: Gráficos -->
                    <div class="tab-pane fade" id="bio-chart" role="tabpanel">
                        <div class="text-center py-4">
                            <i class="bi bi-graph-up fs-1 text-muted"></i>
                            <p class="text-muted mt-2">Gráficos de evolução em desenvolvimento.</p>
                            <p class="text-muted">Em breve você poderá visualizar a evolução das medidas em gráficos.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
