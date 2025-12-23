<x-guest-layout>
    @if (session('status'))
        <div class="alert alert-success mb-3" role="alert">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf
        
        <h4 class="mb-4 text-center fw-semibold">Login</h4>

        <div class="mb-3">
            <label for="email" class="form-label">E-mail</label>
            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username">
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Senha</label>
            <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required autocomplete="current-password">
            @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3 form-check">
            <input type="checkbox" class="form-check-input" id="remember_me" name="remember">
            <label class="form-check-label" for="remember_me">Lembrar de mim</label>
        </div>

        <div class="d-grid gap-2 mb-3">
            <button type="submit" class="btn btn-primary">Entrar</button>
        </div>

        @if (Route::has('password.request'))
            <div class="text-center">
                <a href="{{ route('password.request') }}" class="text-decoration-none">Esqueceu sua senha?</a>
            </div>
        @endif

        @if (Route::has('register'))
            <div class="text-center mt-3">
                <span class="text-muted">Não tem conta?</span>
                <a href="{{ route('register') }}" class="text-decoration-none">Registrar-se</a>
            </div>
        @endif
        
        {{-- Credenciais de Teste --}}
        @if (config('app.env') !== 'production')
            <div class="mt-4 p-3 bg-light border rounded">
                <h6 class="text-muted mb-2 fw-semibold">
                    <i class="bi bi-gear me-1"></i>
                    Credenciais de Teste
                </h6>
                <div class="row g-2">
                    <div class="col-12 col-md-6">
                        <div class="border rounded p-2 bg-white">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <small class="text-muted fw-semibold">Admin</small>
                                    <div class="small text-break">admin@studiofit.com</div>
                                    <div class="small text-muted">password</div>
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="fillLogin('admin@studiofit.com', 'password')">
                                    <i class="bi bi-arrow-right"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="border rounded p-2 bg-white">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <small class="text-muted fw-semibold">Master</small>
                                    <div class="small text-break">master@studiofit.com</div>
                                    <div class="small text-muted">password</div>
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="fillLogin('master@studiofit.com', 'password')">
                                    <i class="bi bi-arrow-right"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <script>
                function fillLogin(email, password) {
                    document.getElementById('email').value = email;
                    document.getElementById('password').value = password;
                    document.getElementById('email').focus();
                }
            </script>
        @endif
    </form>
</x-guest-layout>
