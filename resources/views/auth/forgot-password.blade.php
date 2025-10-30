<x-guest-layout>
    <h4 class="mb-3 text-center fw-semibold">Recuperar Senha</h4>
    
    <div class="mb-4 text-muted small">
        Esqueceu sua senha? Sem problema. Digite seu e-mail e enviaremos um link para redefinir sua senha.
    </div>

    @if (session('status'))
        <div class="alert alert-success mb-3" role="alert">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <div class="mb-3">
            <label for="email" class="form-label">E-mail</label>
            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required autofocus>
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="d-grid gap-2 mb-3">
            <button type="submit" class="btn btn-primary">Enviar Link de Recuperação</button>
        </div>

        <div class="text-center">
            <a href="{{ route('login') }}" class="text-decoration-none">Voltar para login</a>
        </div>
    </form>
</x-guest-layout>
