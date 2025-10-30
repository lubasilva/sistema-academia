<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Academia</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        :root { --primary: #6366f1; --secondary: #f59e0b; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: system-ui, sans-serif; }
        .hero { background: linear-gradient(135deg, var(--primary), #4f46e5); min-height: 100vh; display: flex; align-items: center; padding: 2rem; color: white; }
        .container { max-width: 1200px; margin: 0 auto; }
        h1 { font-size: 3rem; font-weight: 800; margin-bottom: 1rem; }
        .btn { padding: 1rem 2rem; border-radius: 0.5rem; font-weight: 600; text-decoration: none; display: inline-block; }
        .btn-primary { background: var(--secondary); color: white; }
        section { padding: 4rem 2rem; }
        .grid { display: grid; gap: 2rem; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); }
        .card { background: white; padding: 2rem; border-radius: 1rem; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
    </style>
</head>
<body>
    <section class="hero">
        <div class="container">
            <h1>Transforme seu corpo com atenção personalizada</h1>
            <p style="font-size: 1.25rem; margin-bottom: 2rem;">Turmas pequenas, resultados grandes.</p>
            <a href="{{ route('register') }}" class="btn btn-primary">Começar Agora</a>
        </div>
    </section>

    <section style="background: #f9fafb;">
        <div class="container">
            <div class="grid">
                <div class="card" style="text-align: center;">
                    <div style="font-size: 3rem; font-weight: 800; color: var(--primary);">{{ $stats['total_students'] }}+</div>
                    <div>Alunos Ativos</div>
                </div>
                <div class="card" style="text-align: center;">
                    <div style="font-size: 3rem; font-weight: 800; color: var(--primary);">{{ $stats['total_instructors'] }}</div>
                    <div>Instrutores</div>
                </div>
                <div class="card" style="text-align: center;">
                    <div style="font-size: 3rem; font-weight: 800; color: var(--primary);">{{ $stats['satisfaction_rate'] }}%</div>
                    <div>Satisfação</div>
                </div>
            </div>
        </div>
    </section>

    <section>
        <div class="container">
            <h2 style="text-align: center; font-size: 2.5rem; margin-bottom: 3rem;">Nossos Benefícios</h2>
            <div class="grid">
                @foreach($benefits as $benefit)
                <div class="card" style="text-align: center;">
                    <i class="bi bi-{{ $benefit['icon'] }}" style="font-size: 3rem; color: var(--primary);"></i>
                    <h4 style="margin: 1rem 0;">{{ $benefit['title'] }}</h4>
                    <p style="color: #6b7280;">{{ $benefit['description'] }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <section style="background: #f9fafb;">
        <div class="container">
            <h2 style="text-align: center; font-size: 2.5rem; margin-bottom: 3rem;">Planos</h2>
            <div class="grid">
                @foreach($plans as $plan)
                <div class="card" style="text-align: center;">
                    <h3>{{ $plan->name }}</h3>
                    <div style="font-size: 2.5rem; font-weight: 800; color: var(--primary); margin: 1rem 0;">
                        R$ {{ number_format($plan->price, 2, ',', '.') }}
                    </div>
                    <p>{{ $plan->frequency_per_week }}x por semana</p>
                    <a href="{{ route('register') }}" class="btn btn-primary" style="margin-top: 1rem; width: 100%; text-align: center;">Assinar</a>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <footer style="background: #1f2937; color: white; padding: 2rem; text-align: center;">
        <p>&copy; {{ date('Y') }} Academia. Todos os direitos reservados.</p>
    </footer>
</body>
</html>
