@extends('layouts.base')

@section('title', 'Bloquear Horário')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('schedules.index') }}">Horários</a></li>
                    <li class="breadcrumb-item active">Novo Bloqueio</li>
                </ol>
            </nav>
            <h1 class="h3">Bloquear Horário</h1>
            <p class="text-muted">Bloqueie horários para manutenção, feriados ou eventos especiais</p>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('admin.schedule-blocks.store') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label for="date" class="form-label">Data <span class="text-danger">*</span></label>
                            <input type="date" 
                                   class="form-control @error('date') is-invalid @enderror" 
                                   id="date" 
                                   name="date" 
                                   value="{{ old('date', now()->format('Y-m-d')) }}"
                                   min="{{ now()->format('Y-m-d') }}"
                                   required>
                            @error('date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="start_time" class="form-label">Hora Início <span class="text-danger">*</span></label>
                                    <input type="time" 
                                           class="form-control @error('start_time') is-invalid @enderror" 
                                           id="start_time" 
                                           name="start_time" 
                                           value="{{ old('start_time', '00:00') }}"
                                           required>
                                    @error('start_time')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="end_time" class="form-label">Hora Fim <span class="text-danger">*</span></label>
                                    <input type="time" 
                                           class="form-control @error('end_time') is-invalid @enderror" 
                                           id="end_time" 
                                           name="end_time" 
                                           value="{{ old('end_time', '23:59') }}"
                                           required>
                                    @error('end_time')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="reason" class="form-label">Motivo <span class="text-danger">*</span></label>
                            <select class="form-select @error('reason') is-invalid @enderror" 
                                    id="reason" 
                                    name="reason" 
                                    required>
                                <option value="">Selecione um motivo</option>
                                <option value="maintenance" {{ old('reason') === 'maintenance' ? 'selected' : '' }}>
                                    🔧 Manutenção
                                </option>
                                <option value="holiday" {{ old('reason') === 'holiday' ? 'selected' : '' }}>
                                    🎉 Feriado
                                </option>
                                <option value="event" {{ old('reason') === 'event' ? 'selected' : '' }}>
                                    🎪 Evento Especial
                                </option>
                                <option value="other" {{ old('reason') === 'other' ? 'selected' : '' }}>
                                    📝 Outro
                                </option>
                            </select>
                            @error('reason')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label">Observações</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" 
                                      id="notes" 
                                      name="notes" 
                                      rows="3" 
                                      placeholder="Ex: Academia fechada para limpeza profunda">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Máximo 500 caracteres</small>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary" id="blockSubmitBtn">
                                <i class="bi bi-check-circle me-2"></i>Criar Bloqueio
                            </button>
                            <a href="{{ route('schedules.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-x-circle me-2"></i>Cancelar
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card bg-light">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="bi bi-info-circle me-2"></i>Dicas
                    </h5>
                    
                    <h6 class="mt-4">Bloquear o dia todo:</h6>
                    <p class="small text-muted">
                        Use <code>00:00</code> a <code>23:59</code> para bloquear o dia inteiro.
                    </p>

                    <h6 class="mt-3">Bloqueios parciais:</h6>
                    <p class="small text-muted">
                        Você pode bloquear apenas parte do dia. Ex: <code>14:00</code> a <code>16:00</code> para manutenção.
                    </p>

                    <h6 class="mt-3">Múltiplos bloqueios:</h6>
                    <p class="small text-muted">
                        É possível criar vários bloqueios para a mesma data em horários diferentes.
                    </p>

                    <div class="alert alert-warning mt-3 small">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        <strong>Atenção:</strong> Bloqueios não afetam reservas já confirmadas. Cancele-as manualmente se necessário.
                    </div>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-body">
                    <h6 class="card-title">Bloqueios Rápidos</h6>
                    <div class="d-grid gap-2">
                        <button type="button" class="btn btn-outline-primary btn-sm" onclick="quickBlock('today')">
                            Bloquear Hoje
                        </button>
                        <button type="button" class="btn btn-outline-primary btn-sm" onclick="quickBlock('tomorrow')">
                            Bloquear Amanhã
                        </button>
                        <button type="button" class="btn btn-outline-primary btn-sm" onclick="quickBlock('next-holiday')">
                            Próximo Feriado
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function quickBlock(type) {
    const dateInput = document.getElementById('date');
    const startTime = document.getElementById('start_time');
    const endTime = document.getElementById('end_time');
    const reason = document.getElementById('reason');
    
    const today = new Date();
    
    if (type === 'today') {
        dateInput.value = today.toISOString().split('T')[0];
        startTime.value = '00:00';
        endTime.value = '23:59';
    } else if (type === 'tomorrow') {
        const tomorrow = new Date(today);
        tomorrow.setDate(tomorrow.getDate() + 1);
        dateInput.value = tomorrow.toISOString().split('T')[0];
        startTime.value = '00:00';
        endTime.value = '23:59';
    } else if (type === 'next-holiday') {
        dateInput.value = '';
        startTime.value = '00:00';
        endTime.value = '23:59';
        reason.value = 'holiday';
        alert('Selecione a data do feriado');
    }
}

// Proteção contra duplo clique
const blockForm = document.querySelector('form');
const blockSubmitBtn = document.getElementById('blockSubmitBtn');

if (blockForm && blockSubmitBtn) {
    blockForm.addEventListener('submit', function(e) {
        if (blockSubmitBtn.disabled) {
            e.preventDefault();
            return false;
        }
        
        blockSubmitBtn.disabled = true;
        blockSubmitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Criando...';
    });
}
</script>
@endpush
@endsection
