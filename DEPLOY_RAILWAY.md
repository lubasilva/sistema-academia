# Deploy no Railway - Sistema Academia

## 1️⃣ Adicionar Banco de Dados

No dashboard do Railway:
1. Clique em **"+ New"** dentro do seu projeto
2. Selecione **"Database"** > **"Add MySQL"**
3. Aguarde a criação (Railway gera automaticamente as credenciais)

## 2️⃣ Configurar Variáveis de Ambiente

No painel de variáveis do seu app Laravel, adicione:

### Essenciais
```env
APP_NAME="StudioFit Academia"
APP_ENV=production
APP_KEY=base64:XXXXXX  # Gere com: php artisan key:generate --show
APP_DEBUG=false
APP_URL=https://sua-url.railway.app

# Banco de Dados (Railway preenche automaticamente se você usar o plugin MySQL)
DB_CONNECTION=mysql
DB_HOST=${{MySQL.MYSQL_HOST}}
DB_PORT=${{MySQL.MYSQL_PORT}}
DB_DATABASE=${{MySQL.MYSQL_DATABASE}}
DB_USERNAME=${{MySQL.MYSQL_USER}}
DB_PASSWORD=${{MySQL.MYSQL_PASSWORD}}

# Session & Cache
SESSION_DRIVER=database
CACHE_DRIVER=file
QUEUE_CONNECTION=database

# Email (configure depois se necessário)
MAIL_MAILER=log
```

### Railway conecta automaticamente
- Railway detecta as variáveis `${{MySQL.XXX}}` e as preenche automaticamente
- Se preferir PostgreSQL, troque `MySQL` por `Postgres`

## 3️⃣ Comandos de Deploy

O Railway executa automaticamente:
```bash
composer install --no-dev --optimize-autoloader
npm install && npm run build
php artisan config:cache
php artisan route:cache
php artisan migrate --force
php artisan serve --host=0.0.0.0 --port=$PORT
```

## 4️⃣ Primeira vez após deploy

Após o primeiro deploy bem-sucedido, acesse o Terminal no Railway e execute:

```bash
# Criar usuário admin
php artisan tinker
```

Depois execute no tinker:
```php
\App\Models\User::create([
    'name' => 'Admin',
    'email' => 'admin@studiofit.com',
    'password' => bcrypt('password'),
    'role' => 'admin'
]);
```

Ou crie um seeder e execute:
```bash
php artisan db:seed
```

## 5️⃣ Executar Seeders

Se quiser popular exercícios e dados iniciais:
```bash
php artisan db:seed --class=ExerciseSeeder
```

## 🔧 Troubleshooting

### Erro de ENUM no SQLite
Se ver erro relacionado a `MODIFY COLUMN` ou `ENUM`:
- **Causa**: SQLite não suporta ENUM nem ALTER TABLE MODIFY
- **Solução**: Use MySQL ou PostgreSQL (recomendado para produção)

### APP_KEY não definida
```bash
php artisan key:generate --show
```
Copie o resultado e adicione nas variáveis de ambiente do Railway.

### Migrations não rodam
No terminal do Railway:
```bash
php artisan migrate:fresh --force --seed
```
⚠️ CUIDADO: Isso apaga todos os dados!

### Cache precisa ser limpo
```bash
php artisan config:clear
php artisan route:clear
php artisan cache:clear
php artisan view:clear
```

## 📊 Monitoramento

- **Logs**: Railway > Deployments > View Logs
- **Banco de Dados**: Railway > MySQL > Data
- **Custos**: Railway oferece $5 de crédito grátis/mês para hobby

## 🚀 Redeploy Automático

Sempre que você der `git push` no GitHub, o Railway faz deploy automático!

---

## Links Úteis
- Dashboard Railway: https://railway.app/dashboard
- Documentação Laravel Deploy: https://laravel.com/docs/deployment
- Railway Docs: https://docs.railway.app/
