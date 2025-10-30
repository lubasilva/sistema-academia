@extends('layouts.base')

@section('title', 'Criar Horário')

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
                    <h2 class="mb-0">Criar Novo Horário</h2>
                </div>
                <div class="card-body">
                    <form action="{{ route('schedules.store') }}" method="POST">
                        @csrf

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="date" class="form-label">Data *</label>
                                <input 
                                    type="date" 
                                    class="form-control @error('date') is-invalid @enderror" 
                                    id="date" 
                                    name="date" 
                                    value="{{ old('date') }}"
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
                                    value="{{ old('start_time') }}"
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
                                    value="{{ old('end_time') }}"
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
                                    value="{{ old('capacity_override') }}"
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
                                    <option value="open" {{ old('status') === 'open' ? 'selected' : '' }}>Aberto</option>
                                    <option value="closed" {{ old('status') === 'closed' ? 'selected' : '' }}>Fechado</option>
                                    <option value="holiday" {{ old('status') === 'holiday' ? 'selected' : '' }}>Feriado</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i>
                            <strong>Dica:</strong> Você pode criar múltiplos horários rapidamente duplicando este formulário após salvar.
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('schedules.index') }}" class="btn btn-secondary">Cancelar</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle"></i> Criar Horário
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
