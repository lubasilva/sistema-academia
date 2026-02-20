@extends('layouts.base')

@section('title', 'Novo Exercício')

@section('content')
<div class="container-fluid px-3 px-md-4">
    <!-- Header -->
    <div class="mb-4">
        <div class="d-flex align-items-center mb-2">
            <a href="{{ route('exercises.index') }}" class="btn btn-sm btn-outline-secondary me-3">
                <i class="bi bi-arrow-left"></i>
            </a>
            <div>
                <h1 class="h4 mb-0 fw-bold">Novo Exercício</h1>
                <p class="text-muted mb-0 small">Adicione um novo exercício à biblioteca</p>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('exercises.store') }}">
                        @csrf

                        <!-- Nome -->
                        <div class="mb-3">
                            <label for="name" class="form-label fw-semibold">
                                Nome do Exercício <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control @error('name') is-invalid @enderror" 
                                   id="name" 
                                   name="name" 
                                   value="{{ old('name') }}" 
                                   required 
                                   autofocus
                                   placeholder="Ex: Supino Reto">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Grupo Muscular -->
                        <div class="mb-3">
                            <label for="muscle_group" class="form-label fw-semibold">
                                Grupo Muscular <span class="text-danger">*</span>
                            </label>
                            <select class="form-select @error('muscle_group') is-invalid @enderror" 
                                    id="muscle_group" 
                                    name="muscle_group" 
                                    required>
                                <option value="">Selecione...</option>
                                @foreach($muscleGroups as $key => $label)
                                    <option value="{{ $key }}" {{ old('muscle_group') == $key ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            @error('muscle_group')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Categoria -->
                        <div class="mb-3">
                            <label for="category" class="form-label fw-semibold">Categoria</label>
                            <select class="form-select @error('category') is-invalid @enderror" 
                                    id="category" 
                                    name="category">
                                <option value="">Selecione...</option>
                                @foreach($categories as $key => $label)
                                    <option value="{{ $key }}" {{ old('category') == $key ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Equipamento -->
                        <div class="mb-3">
                            <label for="equipment" class="form-label fw-semibold">Equipamento</label>
                            <input type="text" 
                                   class="form-control @error('equipment') is-invalid @enderror" 
                                   id="equipment" 
                                   name="equipment" 
                                   value="{{ old('equipment') }}"
                                   placeholder="Ex: Barra, Halteres, Peso Corporal">
                            @error('equipment')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Descrição -->
                        <div class="mb-3">
                            <label for="description" class="form-label fw-semibold">Descrição</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" 
                                      name="description" 
                                      rows="4"
                                      placeholder="Descreva como executar o exercício, músculos trabalhados, dicas de execução...">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- URL da Imagem -->
                        <div class="mb-3">
                            <label for="image_url" class="form-label fw-semibold">URL da Imagem</label>
                            <input type="url" 
                                   class="form-control @error('image_url') is-invalid @enderror" 
                                   id="image_url" 
                                   name="image_url" 
                                   value="{{ old('image_url') }}"
                                   placeholder="https://exemplo.com/imagem.jpg">
                            @error('image_url')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">URL completa de uma imagem de demonstração do exercício</small>
                        </div>

                        <!-- Status Ativo -->
                        <div class="mb-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input" 
                                       type="checkbox" 
                                       id="is_active" 
                                       name="is_active" 
                                       value="1"
                                       {{ old('is_active', true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">
                                    Exercício ativo (disponível para uso)
                                </label>
                            </div>
                        </div>

                        <!-- Botões -->
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle me-1"></i>
                                Criar Exercício
                            </button>
                            <a href="{{ route('exercises.index') }}" class="btn btn-outline-secondary">
                                Cancelar
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
