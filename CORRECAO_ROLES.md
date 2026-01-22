# Correção de Inconsistências de Roles

## Problema Identificado

O sistema estava apresentando **erros 500 no Railway** ao:
- Cadastrar alunos
- Adicionar planos aos alunos
- Adicionar créditos

**Causa raiz:** Inconsistência nos valores do campo `role` no banco de dados vs. código da aplicação.

### Valores no Banco de Dados (corretos)
```php
'admin', 'instrutor', 'aluno'  // Português
```

### Valores usados erroneamente no código
```php
'admin', 'instructor', 'student'  // Inglês
```

## Arquivos Corrigidos

### Controllers
- ✅ `app/Http/Controllers/UserController.php` - Validações de role
- ✅ `app/Http/Controllers/Admin/StudentManagementController.php` - Queries de alunos
- ✅ `app/Http/Controllers/DashboardController.php` - Estatísticas
- ✅ `app/Http/Controllers/ReportController.php` - Relatórios
- ✅ `app/Http/Controllers/WelcomeController.php` - Landing page
- ✅ `app/Http/Controllers/WorkoutController.php` - Treinos

### Routes
- ✅ `routes/web.php` - API de alunos com créditos

### Views
- ✅ `resources/views/users/create.blade.php` - Formulário de criação
- ✅ `resources/views/users/edit.blade.php` - Formulário de edição
- ✅ `resources/views/users/index.blade.php` - Listagem
- ✅ `resources/views/bookings/index.blade.php` - Agendamentos
- ✅ `resources/views/workouts/index.blade.php` - Treinos
- ✅ `resources/views/dashboard.blade.php` - Dashboard
- ✅ `resources/views/partials/sidebar.blade.php` - Menu lateral

### Seeders
- ✅ `database/seeders/WeeklyUsageSeeder.php` - Simulação semanal
- ✅ `database/seeders/DemoDataSeeder.php` - Dados de demonstração

### Providers
- ✅ `app/Providers/AuthServiceProvider.php` - Gates de autorização

## Mudanças Realizadas

### Antes ❌
```php
// Controllers
User::where('role', 'student')
$request->validate(['role' => 'required|in:admin,instructor,student'])

// Views
<option value="student">Aluno</option>
@if(auth()->user()->role === 'student')

// Gates
Gate::define('instructor-access', function (User $user) {
    return in_array($user->role, ['admin', 'instructor']);
});
```

### Depois ✅
```php
// Controllers
User::where('role', 'aluno')
$request->validate(['role' => 'required|in:admin,instrutor,aluno'])

// Views
<option value="aluno">Aluno</option>
@if(auth()->user()->role === 'aluno')

// Gates
Gate::define('instructor-access', function (User $user) {
    return in_array($user->role, ['admin', 'instrutor']);
});
```

## Próximos Passos

### 1. Deploy para o Railway
```bash
# Commitar as mudanças
git add .
git commit -m "fix: padronizar roles para português (aluno, instrutor) - corrige erros 500"
git push origin main
```

### 2. Verificar no Railway
Após o deploy:
- ✅ Testar cadastro de aluno
- ✅ Testar atribuição de plano
- ✅ Testar adição de créditos

### 3. Verificar Logs
Se ainda houver erros, verificar logs:
```bash
# No Railway
railway logs
```

## Importante

⚠️ **NÃO MISTURAR** valores de role em português e inglês no código.

✅ **SEMPRE USAR:**
- `admin` - Administrador
- `instrutor` - Instrutor
- `aluno` - Aluno

❌ **NUNCA USAR:**
- `instructor`
- `student`

## Checklist Final

- [x] Todos os controllers corrigidos
- [x] Todas as views corrigidas
- [x] Todos os seeders corrigidos
- [x] Routes corrigidas
- [x] AuthServiceProvider corrigido
- [ ] Fazer deploy no Railway
- [ ] Testar funcionalidades no Railway

---
**Data:** 22 de janeiro de 2026
**Autor:** GitHub Copilot
**Status:** ✅ Correções implementadas - Pronto para deploy
