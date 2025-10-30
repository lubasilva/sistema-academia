<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BellaForma Studio - Transforme seu corpo e sua vida</title>
    <meta name="description" content="Academia moderna com foco em resultados. Turmas pequenas, treinos personalizados e acompanhamento profissional.">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        :root { 
            --primary: #2563eb;
            --primary-dark: #1e40af;
            --secondary: #0ea5e9;
            --accent: #f59e0b;
            --dark: #1e293b;
            --light-gray: #f8fafc;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', system-ui, sans-serif; color: var(--dark); line-height: 1.6; }
        
        /* Header/Navbar */
        .navbar { 
            position: fixed; 
            top: 0; 
            width: 100%; 
            background: rgba(255, 255, 255, 0.95); 
            backdrop-filter: blur(10px);
            box-shadow: 0 1px 3px rgba(0,0,0,0.1); 
            z-index: 1000; 
            padding: 1rem 0;
        }
        .navbar .container { display: flex; justify-content: space-between; align-items: center; }
        .logo { height: 60px; }
        .nav-links { display: flex; gap: 2rem; align-items: center; list-style: none; }
        .nav-links a { text-decoration: none; color: var(--dark); font-weight: 500; transition: color 0.3s; }
        .nav-links a:hover { color: var(--primary); }
        
        /* Hero Section */
        .hero { 
            background: linear-gradient(135deg, rgba(37, 99, 235, 0.95), rgba(30, 64, 175, 0.95)),
                        url('https://images.unsplash.com/photo-1534438327276-14e5300c3a48?w=1920&q=80') center/cover;
            min-height: 100vh; 
            display: flex; 
            align-items: center; 
            padding: 6rem 2rem 4rem; 
            color: white;
            text-align: center;
        }
        .hero-content { max-width: 900px; margin: 0 auto; }
        h1 { font-size: clamp(2.5rem, 5vw, 4rem); font-weight: 900; margin-bottom: 1.5rem; line-height: 1.1; }
        .hero p { font-size: clamp(1.1rem, 2vw, 1.5rem); margin-bottom: 2rem; opacity: 0.95; }
        
        .container { max-width: 1200px; margin: 0 auto; padding: 0 1rem; }
        
        /* Buttons */
        .btn { 
            padding: 1rem 2.5rem; 
            border-radius: 50px; 
            font-weight: 600; 
            text-decoration: none; 
            display: inline-block;
            transition: all 0.3s;
            border: none;
            cursor: pointer;
            font-size: 1.1rem;
        }
        .btn-primary { background: var(--accent); color: white; box-shadow: 0 4px 14px rgba(245, 158, 11, 0.4); }
        .btn-primary:hover { background: #d97706; transform: translateY(-2px); box-shadow: 0 6px 20px rgba(245, 158, 11, 0.6); }
        .btn-outline { background: transparent; color: white; border: 2px solid white; }
        .btn-outline:hover { background: white; color: var(--primary); }
        
        /* Sections */
        section { padding: 5rem 2rem; }
        .section-title { 
            text-align: center; 
            font-size: clamp(2rem, 4vw, 3rem); 
            font-weight: 800; 
            margin-bottom: 1rem;
            color: var(--dark);
        }
        .section-subtitle { 
            text-align: center; 
            font-size: 1.2rem; 
            color: #64748b; 
            margin-bottom: 4rem;
            max-width: 700px;
            margin-left: auto;
            margin-right: auto;
        }
        
        /* Grid */
        .grid { display: grid; gap: 2rem; }
        .grid-3 { grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); }
        .grid-2 { grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); }
        
        /* Cards */
        .card { 
            background: white; 
            padding: 2.5rem; 
            border-radius: 1.5rem; 
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
            transition: transform 0.3s, box-shadow 0.3s;
        }
        .card:hover { transform: translateY(-5px); box-shadow: 0 20px 40px rgba(0,0,0,0.12); }
        .card-icon { 
            width: 70px; 
            height: 70px; 
            background: linear-gradient(135deg, var(--primary), var(--secondary)); 
            border-radius: 1rem; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            margin-bottom: 1.5rem;
        }
        .card-icon i { font-size: 2rem; color: white; }
        .card h3 { font-size: 1.5rem; font-weight: 700; margin-bottom: 1rem; }
        .card p { color: #64748b; line-height: 1.8; }
        
        /* Stats */
        .stats { background: var(--light-gray); }
        .stat-card { text-align: center; }
        .stat-number { 
            font-size: clamp(2.5rem, 5vw, 4rem); 
            font-weight: 900; 
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 0.5rem;
        }
        .stat-label { font-size: 1.1rem; color: #64748b; font-weight: 600; }
        
        /* Gallery */
        .gallery-grid { 
            display: grid; 
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); 
            gap: 1rem; 
        }
        .gallery-item { 
            position: relative; 
            overflow: hidden; 
            border-radius: 1rem; 
            aspect-ratio: 1;
            cursor: pointer;
        }
        .gallery-item img { 
            width: 100%; 
            height: 100%; 
            object-fit: cover; 
            transition: transform 0.3s;
        }
        .gallery-item:hover img { transform: scale(1.1); }
        
        /* Testimonials */
        .testimonial { 
            background: white; 
            padding: 2.5rem; 
            border-radius: 1.5rem; 
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
            position: relative;
        }
        .testimonial::before {
            content: '"';
            position: absolute;
            top: -20px;
            left: 20px;
            font-size: 6rem;
            color: var(--primary);
            opacity: 0.1;
            font-family: Georgia, serif;
        }
        .testimonial-text { font-size: 1.1rem; color: #475569; margin-bottom: 1.5rem; font-style: italic; }
        .testimonial-author { display: flex; align-items: center; gap: 1rem; }
        .author-avatar { 
            width: 50px; 
            height: 50px; 
            border-radius: 50%; 
            background: linear-gradient(135deg, var(--primary), var(--secondary)); 
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            font-size: 1.2rem;
        }
        .author-info h4 { font-weight: 600; margin-bottom: 0.2rem; }
        .author-info p { color: #94a3b8; font-size: 0.9rem; }
        
        /* CTA Section */
        .cta { 
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: white;
            text-align: center;
        }
        
        /* Footer */
        footer { 
            background: #0f172a; 
            color: #cbd5e1; 
            padding: 3rem 2rem 1.5rem; 
        }
        .footer-content { 
            display: grid; 
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); 
            gap: 3rem;
            margin-bottom: 2rem;
        }
        .footer-section h4 { color: white; margin-bottom: 1rem; font-size: 1.2rem; }
        .footer-section ul { list-style: none; }
        .footer-section ul li { margin-bottom: 0.5rem; }
        .footer-section a { color: #cbd5e1; text-decoration: none; transition: color 0.3s; }
        .footer-section a:hover { color: white; }
        .footer-bottom { 
            border-top: 1px solid #334155; 
            padding-top: 1.5rem; 
            text-align: center;
            color: #94a3b8;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .nav-links { display: none; }
            .hero { padding: 5rem 1rem 3rem; }
            section { padding: 3rem 1rem; }
            .grid-2, .grid-3 { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar">
        <div class="container">
            <img src="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTAwIiBoZWlnaHQ9IjYwIiB2aWV3Qm94PSIwIDAgMTAwIDYwIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciPjxkZWZzPjxsaW5lYXJHcmFkaWVudCBpZD0iYSIgeDE9IjAlIiB5MT0iMCUiIHgyPSIxMDAlIiB5Mj0iMTAwJSI+PHN0b3Agb2Zmc2V0PSIwJSIgc3RvcC1jb2xvcj0iI2RkZCIvPjxzdG9wIG9mZnNldD0iMTAwJSIgc3RvcC1jb2xvcj0iIzk5OSIvPjwvbGluZWFyR3JhZGllbnQ+PGxpbmVhckdyYWRpZW50IGlkPSJiIiB4MT0iMCUiIHkxPSIwJSIgeDI9IjEwMCUiIHkyPSIxMDAlIj48c3RvcCBvZmZzZXQ9IjAlIiBzdG9wLWNvbG9yPSIjMjU2M2ViIi8+PHN0b3Agb2Zmc2V0PSIxMDAlIiBzdG9wLWNvbG9yPSIjMGVhNWU5Ii8+PC9saW5lYXJHcmFkaWVudD48L2RlZnM+PHBhdGggZD0iTTIwIDEwIEw0MCAxMCBMNTAgMzAgTDQwIDUwIEwyMCA1MCBaIiBmaWxsPSJ1cmwoI2EpIi8+PHBhdGggZD0iTTQ1IDEwIEw2NSAxMCBMNzUgMzAgTDY1IDUwIEw0NSA1MCBaIiBmaWxsPSJ1cmwoI2IpIi8+PC9zdmc+" alt="BellaForma Studio" class="logo">
            <ul class="nav-links">
                <li><a href="#beneficios">Benefícios</a></li>
                <li><a href="#planos">Planos</a></li>
                <li><a href="#depoimentos">Depoimentos</a></li>
                <li><a href="#galeria">Galeria</a></li>
                <li><a href="{{ route('login') }}" class="btn btn-primary" style="padding: 0.7rem 1.5rem; font-size: 0.95rem;">Entrar</a></li>
            </ul>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-content">
            <h1>Transforme seu corpo com atenção personalizada</h1>
            <p>Na BellaForma Studio, você encontra treinos personalizados, turmas pequenas e resultados comprovados. Mais do que uma academia, uma família fitness.</p>
            <div style="display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap; margin-top: 2rem;">
                <a href="{{ route('register') }}" class="btn btn-primary">
                    <i class="bi bi-rocket-takeoff"></i> Começar Agora
                </a>
                <a href="#planos" class="btn btn-outline">
                    <i class="bi bi-play-circle"></i> Ver Planos
                </a>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="stats">
        <div class="container">
            <div class="grid grid-3">
                <div class="stat-card">
                    <div class="stat-number">{{ $stats['total_students'] }}+</div>
                    <div class="stat-label">Alunos Conquistando Resultados</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number">{{ $stats['total_instructors'] }}</div>
                    <div class="stat-label">Instrutores Certificados</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number">{{ $stats['satisfaction_rate'] }}%</div>
                    <div class="stat-label">Taxa de Satisfação</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Benefits Section -->
    <section id="beneficios">
        <div class="container">
            <h2 class="section-title">Por que escolher a BellaForma?</h2>
            <p class="section-subtitle">Oferecemos uma experiência única de treinamento, com foco total em você e seus objetivos.</p>
            
            <div class="grid grid-3">
                <div class="card">
                    <div class="card-icon">
                        <i class="bi bi-people"></i>
                    </div>
                    <h3>Turmas Pequenas</h3>
                    <p>Máximo de 10 alunos por aula, garantindo atenção individualizada e acompanhamento próximo de cada movimento.</p>
                </div>
                
                <div class="card">
                    <div class="card-icon">
                        <i class="bi bi-award"></i>
                    </div>
                    <h3>Profissionais Certificados</h3>
                    <p>Equipe de instrutores especializados e certificados, com anos de experiência em transformações reais.</p>
                </div>
                
                <div class="card">
                    <div class="card-icon">
                        <i class="bi bi-graph-up-arrow"></i>
                    </div>
                    <h3>Resultados Comprovados</h3>
                    <p>Metodologia testada e aprovada por centenas de alunos que alcançaram seus objetivos de forma saudável.</p>
                </div>
                
                <div class="card">
                    <div class="card-icon">
                        <i class="bi bi-calendar-check"></i>
                    </div>
                    <h3>Horários Flexíveis</h3>
                    <p>Aulas de segunda a sexta, das 6h às 22h. Reserve pelo app e treine no seu tempo, sem burocracia.</p>
                </div>
                
                <div class="card">
                    <div class="card-icon">
                        <i class="bi bi-heart-pulse"></i>
                    </div>
                    <h3>Avaliação Física</h3>
                    <p>Acompanhamento completo com avaliações periódicas, medições e ajustes no treino conforme sua evolução.</p>
                </div>
                
                <div class="card">
                    <div class="card-icon">
                        <i class="bi bi-phone"></i>
                    </div>
                    <h3>App Exclusivo</h3>
                    <p>Gerencie suas aulas, veja seu histórico, créditos disponíveis e receba lembretes - tudo na palma da mão.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Gallery Section -->
    <section id="galeria" style="background: var(--light-gray);">
        <div class="container">
            <h2 class="section-title">Nossos Espaços</h2>
            <p class="section-subtitle">Ambientes modernos, equipamentos de última geração e um clima motivador.</p>
            
            <div class="gallery-grid">
                <div class="gallery-item">
                    <img src="https://images.unsplash.com/photo-1534438327276-14e5300c3a48?w=600&q=80" alt="Área de treino funcional">
                </div>
                <div class="gallery-item">
                    <img src="https://images.unsplash.com/photo-1571902943202-507ec2618e8f?w=600&q=80" alt="Equipamentos modernos">
                </div>
                <div class="gallery-item">
                    <img src="https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=600&q=80" alt="Espaço de musculação">
                </div>
                <div class="gallery-item">
                    <img src="https://images.unsplash.com/photo-1549576490-b0b4831ef60a?w=600&q=80" alt="Área de alongamento">
                </div>
                <div class="gallery-item">
                    <img src="https://images.unsplash.com/photo-1517836357463-d25dfeac3438?w=600&q=80" alt="Aulas em grupo">
                </div>
                <div class="gallery-item">
                    <img src="https://images.unsplash.com/photo-1593079831268-3381b0db4a77?w=600&q=80" alt="Vestiários modernos">
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section id="depoimentos">
        <div class="container">
            <h2 class="section-title">O que nossos alunos dizem</h2>
            <p class="section-subtitle">Histórias reais de transformação e superação.</p>
            
            <div class="grid grid-3">
                <div class="testimonial">
                    <p class="testimonial-text">"Perdi 15kg em 6 meses e ganhei muito mais disposição. A atenção dos professores faz toda diferença! Nunca me senti tão bem comigo mesma."</p>
                    <div class="testimonial-author">
                        <div class="author-avatar">M</div>
                        <div class="author-info">
                            <h4>Maria Silva</h4>
                            <p>Aluna há 8 meses</p>
                        </div>
                    </div>
                </div>
                
                <div class="testimonial">
                    <p class="testimonial-text">"Ambiente acolhedor e motivador. As turmas pequenas permitem um acompanhamento personalizado que eu nunca tive em outras academias."</p>
                    <div class="testimonial-author">
                        <div class="author-avatar">J</div>
                        <div class="author-info">
                            <h4>João Santos</h4>
                            <p>Aluno há 1 ano</p>
                        </div>
                    </div>
                </div>
                
                <div class="testimonial">
                    <p class="testimonial-text">"Finalmente encontrei uma academia onde me sinto em casa. Os resultados vieram rápido e de forma sustentável. Recomendo demais!"</p>
                    <div class="testimonial-author">
                        <div class="author-avatar">A</div>
                        <div class="author-info">
                            <h4>Ana Costa</h4>
                            <p>Aluna há 5 meses</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Plans Section -->
    <section id="planos" style="background: var(--light-gray);">
        <div class="container">
            <h2 class="section-title">Escolha seu plano</h2>
            <p class="section-subtitle">Flexibilidade para você treinar no seu ritmo. Todos os planos incluem avaliação física e app exclusivo.</p>
            
            <div class="grid grid-3">
                @foreach($plans as $plan)
                <div class="card" style="text-align: center; {{ $plan->frequency_per_week == 3 ? 'border: 3px solid var(--primary); position: relative;' : '' }}">
                    @if($plan->frequency_per_week == 3)
                        <div style="position: absolute; top: -15px; left: 50%; transform: translateX(-50%); background: var(--accent); color: white; padding: 0.4rem 1.5rem; border-radius: 20px; font-weight: 700; font-size: 0.85rem;">
                            MAIS POPULAR
                        </div>
                    @endif
                    <h3 style="font-size: 1.8rem; margin-bottom: 0.5rem;">{{ $plan->name }}</h3>
                    <p style="color: #64748b; margin-bottom: 1.5rem;">{{ $plan->billing_cycle_name }}</p>
                    <div style="font-size: 3rem; font-weight: 900; background: linear-gradient(135deg, var(--primary), var(--secondary)); -webkit-background-clip: text; -webkit-text-fill-color: transparent; margin: 1.5rem 0;">
                        R$ {{ number_format($plan->price, 2, ',', '.') }}
                    </div>
                    <p style="color: #64748b; margin-bottom: 2rem;">
                        <strong>{{ $plan->frequency_per_week }}x por semana</strong><br>
                        <small>Aulas de 1 hora</small>
                    </p>
                    <ul style="list-style: none; text-align: left; margin: 2rem 0;">
                        <li style="padding: 0.5rem 0; color: #475569;">
                            <i class="bi bi-check-circle-fill" style="color: var(--primary); margin-right: 0.5rem;"></i>
                            Acesso ao app exclusivo
                        </li>
                        <li style="padding: 0.5rem 0; color: #475569;">
                            <i class="bi bi-check-circle-fill" style="color: var(--primary); margin-right: 0.5rem;"></i>
                            Avaliação física mensal
                        </li>
                        <li style="padding: 0.5rem 0; color: #475569;">
                            <i class="bi bi-check-circle-fill" style="color: var(--primary); margin-right: 0.5rem;"></i>
                            Horários flexíveis
                        </li>
                        <li style="padding: 0.5rem 0; color: #475569;">
                            <i class="bi bi-check-circle-fill" style="color: var(--primary); margin-right: 0.5rem;"></i>
                            Turmas de até 10 alunos
                        </li>
                    </ul>
                    <a href="{{ route('register') }}" class="btn btn-primary" style="width: 100%; text-align: center;">
                        Assinar Agora
                    </a>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta">
        <div class="container">
            <h2 class="section-title" style="color: white;">Pronto para transformar sua vida?</h2>
            <p style="font-size: 1.2rem; margin-bottom: 2rem; opacity: 0.95; max-width: 600px; margin-left: auto; margin-right: auto;">
                Junte-se a centenas de alunos que já conquistaram seus objetivos. Sua jornada começa aqui!
            </p>
            <a href="{{ route('register') }}" class="btn" style="background: white; color: var(--primary); font-size: 1.1rem;">
                <i class="bi bi-rocket-takeoff"></i> Começar Minha Transformação
            </a>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h4>BellaForma Studio</h4>
                    <p style="margin-bottom: 1rem; line-height: 1.8;">
                        Transformando vidas através do movimento. Mais que uma academia, uma família fitness.
                    </p>
                    <div style="display: flex; gap: 1rem; font-size: 1.5rem;">
                        <a href="#"><i class="bi bi-instagram"></i></a>
                        <a href="#"><i class="bi bi-facebook"></i></a>
                        <a href="#"><i class="bi bi-whatsapp"></i></a>
                    </div>
                </div>
                
                <div class="footer-section">
                    <h4>Links Rápidos</h4>
                    <ul>
                        <li><a href="#beneficios">Benefícios</a></li>
                        <li><a href="#planos">Planos</a></li>
                        <li><a href="#depoimentos">Depoimentos</a></li>
                        <li><a href="#galeria">Galeria</a></li>
                        <li><a href="{{ route('login') }}">Área do Aluno</a></li>
                    </ul>
                </div>
                
                <div class="footer-section">
                    <h4>Horário de Funcionamento</h4>
                    <ul>
                        <li>Segunda a Sexta: 6h - 22h</li>
                        <li>Sábado: 8h - 14h</li>
                        <li>Domingo: Fechado</li>
                    </ul>
                </div>
                
                <div class="footer-section">
                    <h4>Contato</h4>
                    <ul>
                        <li><i class="bi bi-geo-alt"></i> Rua Exemplo, 123 - Centro</li>
                        <li><i class="bi bi-telephone"></i> (11) 9999-9999</li>
                        <li><i class="bi bi-envelope"></i> contato@bellaforma.com</li>
                    </ul>
                </div>
            </div>
            
            <div class="footer-bottom">
                <p>&copy; {{ date('Y') }} BellaForma Studio. Todos os direitos reservados.</p>
            </div>
        </div>
    </footer>

    <script>
        // Smooth scroll
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            });
        });
        
        // Navbar scroll effect
        window.addEventListener('scroll', () => {
            const navbar = document.querySelector('.navbar');
            if (window.scrollY > 50) {
                navbar.style.boxShadow = '0 4px 12px rgba(0,0,0,0.15)';
            } else {
                navbar.style.boxShadow = '0 1px 3px rgba(0,0,0,0.1)';
            }
        });
    </script>
</body>
</html>
