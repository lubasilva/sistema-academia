@extends('layouts.base')

@section('title', 'Editar Bloqueio')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('schedules.index') }}">Horários</a></li>
                    <li class="breadcrumb-item active">Editar Bloqueio</li>
                </ol>
            </nav>
            <h1 class="h3">Editar Bloqueio</h1>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('admin.schedule-blocks.update', $scheduleBlock) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="date" class="form-label">Data <span class="text-danger">*</span></label>
                            <input type="date" 
                                   class="form-control @error('date') is-invalid @enderror" 
                                   id="date" 
                                   name="date" 
                                   value="{{ old('date', $scheduleBlock->date->format('Y-m-d')) }}"
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
                                           value="{{ old('start_time', substr($scheduleBlock->start_time, 0, 5)) }}"
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
                                           value="{{ old('end_time', substr($scheduleBlock->end_time, 0, 5)) }}"
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
                                <option value="maintenance" {{ old('reason', $scheduleBlock->reason) === 'maintenance' ? 'selected' : '' }}>
                                    🔧 Manutenção
                                </option>
                                <option value="holiday" {{ old('reason', $scheduleBlock->reason) === 'holiday' ? 'selected' : '' }}>
                                    🎉 Feriado
                                </option>
                                <option value="event" {{ old('reason', $scheduleBlock->reason) === 'event' ? 'selected' : '' }}>
                                    🎪 Evento Especial
                                </option>
                                <option value="other" {{ old('reason', $scheduleBlock->reason) === 'other' ? 'selected' : '' }}>
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
                                      rows="3">{{ old('notes', $scheduleBlock->notes) }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Máximo 500 caracteres</small>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle me-2"></i>Atualizar Bloqueio
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
                        <i class="bi bi-info-circle me-2"></i>Informações do Bloqueio
                    </h5>
                    
                    <hr>
                    
                    <div class="mb-2">
                        <small class="text-muted">Criado em:</small>
                        <p class="mb-0">{{ $scheduleBlock->created_at->format('d/m/Y H:i') }}</p>
                    </div>

                    @if($scheduleBlock->creator)
                        <div class="mb-2">
                            <small class="text-muted">Criado por:</small>
                            <p class="mb-0">{{ $scheduleBlock->creator->name }}</p>
                        </div>
                    @endif

                    @if($scheduleBlock->updated_at != $scheduleBlock->created_at)
                        <div class="mb-2">
                            <small class="text-muted">Última atualização:</small>
                            <p class="mb-0">{{ $scheduleBlock->updated_at->format('d/m/Y H:i') }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <div class="card mt-3 border-danger">
                <div class="card-body">
                    <h6 class="card-title text-danger">
                        <i class="bi bi-trash me-2"></i>Zona de Perigo
                    </h6>
                    <p class="small text-muted">
                        Remover este bloqueio liberará os horários para agendamento novamente.
                    </p>
                    <form action="{{ route('admin.schedule-blocks.destroy', $scheduleBlock) }}" 
                          method="POST"
                          onsubmit="return confirm('Tem certeza que deseja remover este bloqueio?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm w-100">
                            <i class="bi bi-trash me-2"></i>Remover Bloqueio
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
