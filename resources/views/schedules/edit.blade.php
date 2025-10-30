@extends('layouts.base')

@section('title', 'Editar Horário')

@section('content')
<div class="container-fluid">
    <div class="mb-4">
        <a href="{{ route('schedules.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Voltar
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h2 class="mb-0">Editar Horário</h2>
                </div>
                <div class="card-body">
                    <form action="{{ route('schedules.update', $schedule) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="date" class="form-label">Data *</label>
                                <input 
                                    type="date" 
                                    class="form-control @error('date') is-invalid @enderror" 
                                    id="date" 
                                    name="date" 
                                    value="{{ old('date', $schedule->starts_at->format('Y-m-d')) }}"
                                    required>
                                @error('date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-3 mb-3">
                                <label for="start_time" class="form-label">Hora Início *</label>
                                <input 
                                    type="time" 
                                    class="form-control @error('start_time') is-invalid @enderror" 
                                    id="start_time" 
                                    name="start_time" 
                                    value="{{ old('start_time', $schedule->starts_at->format('H:i')) }}"
                                    required>
                                @error('start_time')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-3 mb-3">
                                <label for="end_time" class="form-label">Hora Fim *</label>
                                <input 
                                    type="time" 
                                    class="form-control @error('end_time') is-invalid @enderror" 
                                    id="end_time" 
                                    name="end_time" 
                                    value="{{ old('end_time', $schedule->ends_at->format('H:i')) }}"
                                    required>
                                @error('end_time')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="capacity_override" class="form-label">Capacidade Customizada</label>
                                <input 
                                    type="number" 
                                    class="form-control @error('capacity_override') is-invalid @enderror" 
                                    id="capacity_override" 
                                    name="capacity_override" 
                                    value="{{ old('capacity_override', $schedule->capacity_override) }}"
                                    min="1"
                                    max="50"
                                    placeholder="Deixe vazio para usar capacidade padrão">
                                <small class="text-muted">
                                    Capacidade padrão: {{ \App\Models\Setting::where('key', 'max_capacity_per_class')->value('value') ?? 10 }} pessoas
                                </small>
                                @error('capacity_override')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="status" class="form-label">Status *</label>
                                <select 
                                    class="form-select @error('status') is-invalid @enderror" 
                                    id="status" 
                                    name="status"
                                    required>
                                    <option value="open" {{ old('status', $schedule->status) === 'open' ? 'selected' : '' }}>Aberto</option>
                                    <option value="closed" {{ old('status', $schedule->status) === 'closed' ? 'selected' : '' }}>Fechado</option>
                                    <option value="holiday" {{ old('status', $schedule->status) === 'holiday' ? 'selected' : '' }}>Feriado</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        @if($schedule->bookings()->count() > 0)
                            <div class="alert alert-warning">
                                <i class="bi bi-exclamation-triangle"></i>
                                <strong>Atenção:</strong> Este horário possui {{ $schedule->bookings()->count() }} reserva(s). 
                                Alterações podem afetar os alunos já inscritos.
                            </div>
                        @endif

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('schedules.index') }}" class="btn btn-secondary">Cancelar</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle"></i> Salvar Alterações
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
