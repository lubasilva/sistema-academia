<aside class="sidebar bg-dark" style="width: 250px;">
    <div class="p-4">
        <h5 class="text-white fw-bold mb-4">
            <i class="bi bi-calendar-check"></i> StudioFit
        </h5>
        <nav class="nav flex-column">
            <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>
            
            <a href="{{ route('bookings.index') }}" class="nav-link {{ request()->routeIs('bookings.*') ? 'active' : '' }}">
                <i class="bi bi-calendar-week"></i> Agenda
            </a>
            
            @if(auth()->user()->role === 'admin')
            <hr class="bg-secondary">
            <h6 class="text-white-50 text-uppercase small px-3">Administração</h6>
            
            <a href="{{ route('users.index') }}" class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
                <i class="bi bi-people"></i> Usuários
            </a>
            
            <a href="{{ route('plans.index') }}" class="nav-link {{ request()->routeIs('plans.*') ? 'active' : '' }}">
                <i class="bi bi-card-list"></i> Planos
            </a>
            
            <a href="{{ route('admin.payments.index') }}" class="nav-link {{ request()->routeIs('admin.payments.*') ? 'active' : '' }}">
                <i class="bi bi-cash-stack"></i> Pagamentos
            </a>
            
            <a href="{{ route('schedules.index') }}" class="nav-link {{ request()->routeIs('schedules.*') ? 'active' : '' }}">
                <i class="bi bi-clock-history"></i> Horários
            </a>
            
            <a href="{{ route('settings.index') }}" class="nav-link {{ request()->routeIs('settings.*') ? 'active' : '' }}">
                <i class="bi bi-gear"></i> Configurações
            </a>
            
            <a href="{{ route('reports.index') }}" class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                <i class="bi bi-file-earmark-text"></i> Relatórios
            </a>
            @endif
            
            @if(auth()->user()->role === 'instructor')
            <hr class="bg-secondary">
            <h6 class="text-white-50 text-uppercase small px-3">Instrutor</h6>
            
            <a href="#" class="nav-link">
                <i class="bi bi-calendar-check"></i> Minhas Aulas
            </a>
            
            <a href="#" class="nav-link">
                <i class="bi bi-clipboard-check"></i> Presenças
            </a>
            @endif
        </nav>
    </div>
</aside>