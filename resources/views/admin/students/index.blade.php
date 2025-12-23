@extends('layouts.base')

@section('title', 'Gerenciamento de Alunos')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col">
            <h2 class="fw-bold">👥 Gerenciamento de Alunos</h2>
        </div>
    </div>
    
    <div class="row">
        <div class="col-12">
            <!-- Barra de pesquisa e filtros -->
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.students.index') }}" class="row g-3">
                        <div class="col-md-6">
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="bi bi-search"></i>
                                </span>
                                <input type="text" 
                                       class="form-control" 
                                       name="search" 
                                       placeholder="Buscar por nome ou email..." 
                                       value="{{ $search }}">
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <select name="status" class="form-select">
                                <option value="all" {{ $status === 'all' ? 'selected' : '' }}>Todos</option>
                                <option value="active" {{ $status === 'active' ? 'selected' : '' }}>Com plano ativo</option>
                                <option value="inactive" {{ $status === 'inactive' ? 'selected' : '' }}>Sem plano ativo</option>
                            </select>
                        </div>
                        
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-funnel"></i> Filtrar
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Estatísticas rápidas -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card border-primary">
                        <div class="card-body text-center">
                            <h3 class="text-primary">{{ $students->total() }}</h3>
                            <p class="mb-0 text-muted">Total de Alunos</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-success">
                        <div class="card-body text-center">
                            <h3 class="text-success">{{ $students->where(fn($s) => $s->stats['has_active_plan'])->count() }}</h3>
                            <p class="mb-0 text-muted">Planos Ativos</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-warning">
                        <div class="card-body text-center">
                            <h3 class="text-warning">{{ $students->filter(fn($s) => $s->stats['has_active_plan'] && $s->stats['total_credits'] <= 2)->count() }}</h3>
                            <p class="mb-0 text-muted">Créditos Baixos</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-info">
                        <div class="card-body text-center">
                            <h3 class="text-info">{{ $students->where(fn($s) => $s->stats['extra_credits'] > 0)->count() }}</h3>
                            <p class="mb-0 text-muted">Com Créditos Extra</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabela de alunos -->
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">📋 Lista de Alunos</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Aluno</th>
                                    <th>Plano Ativo</th>
                                    <th class="text-center">Créditos</th>
                                    <th class="text-center">Taxa Presença</th>
                                    <th class="text-center">Última Aula</th>
                                    <th class="text-center">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($students as $student)
                                    <tr>
                                        <td>
                                            <div>
                                                <strong>{{ $student->name }}</strong>
                                                <br>
                                                <small class="text-muted">{{ $student->email }}</small>
                                            </div>
                                        </td>
                                        <td>
                                            @if($student->activePlan)
                                                <span class="badge bg-success">
                                                    {{ $student->activePlan->plan->name }}
                                                </span>
                                                <br>
                                                <small class="text-muted">
                                                    até {{ \Carbon\Carbon::parse($student->activePlan->end_date)->format('d/m/Y') }}
                                                </small>
                                            @else
                                                <span class="badge bg-secondary">Sem plano</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if($student->stats['has_active_plan'])
                                                <div>
                                                    <span class="badge bg-{{ $student->stats['total_credits'] <= 2 ? 'warning' : 'primary' }}">
                                                        {{ $student->stats['total_credits'] }} total
                                                    </span>
                                                </div>
                                                <small class="text-muted">
                                                    {{ $student->stats['regular_credits'] }} regular
                                                    @if($student->stats['extra_credits'] > 0)
                                                        + {{ $student->stats['extra_credits'] }} extra
                                                    @endif
                                                </small>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if($student->stats['total_bookings'] > 0)
                                                <div>
                                                    <strong class="text-{{ $student->stats['attendance_rate'] >= 80 ? 'success' : ($student->stats['attendance_rate'] >= 60 ? 'warning' : 'danger') }}">
                                                        {{ $student->stats['attendance_rate'] }}%
                                                    </strong>
                                                </div>
                                                <small class="text-muted">
                                                    {{ $student->stats['attended'] }}/{{ $student->stats['total_bookings'] }} aulas
                                                </small>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if($student->stats['last_attendance'])
                                                <small>
                                                    {{ \Carbon\Carbon::parse($student->stats['last_attendance']->date)->format('d/m/Y') }}
                                                </small>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ route('admin.students.show', $student) }}" 
                                               class="btn btn-sm btn-primary"
                                               title="Ver detalhes">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4">
                                            <p class="text-muted mb-0">Nenhum aluno encontrado.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                
                @if($students->hasPages())
                    <div class="card-footer">
                        {{ $students->links('pagination::bootstrap-5') }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
