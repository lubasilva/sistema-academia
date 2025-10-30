@extends('layouts.base')

@section('title', 'Configurações')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0">Configurações</h2>
            <p class="text-muted mb-0 small">Configure o sistema</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form action="{{ route('settings.update') }}" method="POST">
        @csrf
        @method('PUT')

        <!-- Configurações do Sistema -->
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white border-0 py-3">
                <h5 class="mb-0"><i class="bi bi-gear-fill text-primary me-2"></i>Sistema</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="app_name" class="form-label fw-semibold">
                            <i class="bi bi-app text-muted"></i> Nome da Academia
                        </label>
                        <input 
                            type="text" 
                            class="form-control @error('app_name') is-invalid @enderror" 
                            id="app_name" 
                            name="app_name" 
                            value="{{ old('app_name', $settings->get('app_name')->value ?? 'Academia') }}"
                            required>
                        @error('app_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="max_capacity_per_class" class="form-label fw-semibold">
                            <i class="bi bi-people text-muted"></i> Capacidade Padrão por Aula
                        </label>
                        <input 
                            type="number" 
                            class="form-control @error('max_capacity_per_class') is-invalid @enderror" 
                            id="max_capacity_per_class" 
                            name="max_capacity_per_class" 
                            value="{{ old('max_capacity_per_class', $settings->get('max_capacity_per_class')->value ?? 10) }}"
                            min="1"
                            max="50"
                            required>
                        @error('max_capacity_per_class')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Número máximo de alunos por aula</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Configurações de Agendamento -->
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white border-0 py-3">
                <h5 class="mb-0"><i class="bi bi-calendar-check text-primary me-2"></i>Agendamento</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="booking_advance_days" class="form-label fw-semibold">
                            <i class="bi bi-calendar-range text-muted"></i> Dias de Antecedência
                        </label>
                        <input 
                            type="number" 
                            class="form-control @error('booking_advance_days') is-invalid @enderror" 
                            id="booking_advance_days" 
                            name="booking_advance_days" 
                            value="{{ old('booking_advance_days', $settings->get('booking_advance_days')->value ?? 7) }}"
                            min="1"
                            max="90"
                            required>
                        @error('booking_advance_days')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Quantos dias no futuro podem ser agendados</small>
                    </div>

                    <div class="col-md-6">
                        <label for="cancellation_hours" class="form-label fw-semibold">
                            <i class="bi bi-clock-history text-muted"></i> Prazo de Cancelamento (horas)
                        </label>
                        <input 
                            type="number" 
                            class="form-control @error('cancellation_hours') is-invalid @enderror" 
                            id="cancellation_hours" 
                            name="cancellation_hours" 
                            value="{{ old('cancellation_hours', $settings->get('cancellation_hours')->value ?? 24) }}"
                            min="1"
                            max="72"
                            required>
                        @error('cancellation_hours')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Horas antes da aula para cancelar sem penalidade</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Informações de Contato -->
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white border-0 py-3">
                <h5 class="mb-0"><i class="bi bi-telephone text-primary me-2"></i>Contato</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="contact_email" class="form-label fw-semibold">
                            <i class="bi bi-envelope text-muted"></i> E-mail
                        </label>
                        <input 
                            type="email" 
                            class="form-control @error('contact_email') is-invalid @enderror" 
                            id="contact_email" 
                            name="contact_email" 
                            value="{{ old('contact_email', $settings->get('contact_email')->value ?? '') }}"
                            required>
                        @error('contact_email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="contact_phone" class="form-label fw-semibold">
                            <i class="bi bi-phone text-muted"></i> Telefone
                        </label>
                        <input 
                            type="text" 
                            class="form-control @error('contact_phone') is-invalid @enderror" 
                            id="contact_phone" 
                            name="contact_phone" 
                            value="{{ old('contact_phone', $settings->get('contact_phone')->value ?? '') }}"
                            placeholder="(11) 99999-9999">
                        @error('contact_phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <label for="address" class="form-label fw-semibold">
                            <i class="bi bi-geo-alt text-muted"></i> Endereço
                        </label>
                        <textarea 
                            class="form-control @error('address') is-invalid @enderror" 
                            id="address" 
                            name="address" 
                            rows="3"
                            placeholder="Rua, número, bairro, cidade - UF">{{ old('address', $settings->get('address')->value ?? '') }}</textarea>
                        @error('address')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Botões de Ação -->
        <div class="d-flex gap-2 mb-4">
            <button type="submit" class="btn btn-primary flex-grow-1 flex-md-grow-0">
                <i class="bi bi-check-circle me-2"></i>Salvar Alterações
            </button>
            <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary flex-grow-1 flex-md-grow-0">
                Cancelar
            </a>
        </div>
    </form>
</div>
@endsection
