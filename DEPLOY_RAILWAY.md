# Deploy no Railway - Sistema Academia

## 1️⃣ Adicionar Banco de Dados

No dashboard do Railway:
1. Clique em **"+ New"** dentro do seu projeto
2. Selecione **"Database"** > **"Add MySQL"**
3. Aguarde a criação (Railway gera automaticamente as credenciais)

## 2️⃣ Configurar Variáveis de Ambiente

No painel de variáveis do seu app Laravel, adicione:

### ⚠️ IMPORTANTE: Configure as variáveis ANTES do MySQL estar conectado

**Passo 1: Gerar APP_KEY**
No terminal local, execute:
```bash
php artisan key:generate --show
```
Copie o resultado (começa com `base64:`)

**Passo 2: Adicionar variáveis básicas**
No Railway, adicione estas variáveis:
```env
APP_NAME="StudioFit Academia"
APP_ENV=production
APP_KEY=base64:COLE_AQUI_O_RESULTADO_DO_COMANDO_ACIMA
APP_DEBUG=false
APP_URL=https://sistema-academia-production-08a9.up.railway.app

SESSION_DRIVER=database
CACHE_DRIVER=file
QUEUE_CONNECTION=database
MAIL_MAILER=log
LOG_CHANNEL=errorlog
```

**⚠️ NÃO CONFIGURE PORTA MANUALMENTE!**
O Railway usa automaticamente a variável `$PORT`. Não adicione `PORT=8080` nas variáveis.

**Método 1: Cópia Manual (RECOMENDADO)**
1. Clique no serviço **MySQL** no Railway
2. Vá na aba **"Connect"**
3. Copie o endereço do **"Private Networking"** (algo como `mysql.railway.internal` ou similar)
4. No serviço **Laravel**, vá em **"Variables"** e adicione:

```env
DB_CONNECTION=mysql
DB_HOST=mysql.railway.internal
DB_PORT=3306
DB_DATABASE=railway
DB_USERNAME=root
DB_PASSWORD=COLE_AQUI_O_PASSWORD_DO_MYSQL
```

**⚠️ IMPORTANTE**: 
- **NÃO use aspas** nas variáveis
- **NÃO use `${{}}`** - isso são referências internas do Railway
- Cole os valores DIRETOS
- Para o `DB_HOST`, use o **Private Domain** do MySQL (encontrado na aba "Connect")
- Para o `DB_PASSWORD`, copie o valor da variável `MYSQL_ROOT_PASSWORD` do MySQL

**Método 2: Usar Shared Variables (alternativo)**
1. No projeto Railway (não no serviço), vá em **"Shared Variables"**
2. Clique em **"New Shared Variable"**
3. Crie variáveis compartilhadas apontando para o MySQL
4. No serviço Laravel, referencie essas variáveis compartilhadas

## 3️⃣ Comandos de Deploy

O Railway executa automaticamente:
```bash
composer install --no-dev --optimize-autoloader
npm install && npm run build
php artisan serve --host=0.0.0.0 --port=$PORT
```

### ⚠️ NÃO rode migrations no startCommand!
As migrations devem ser executadas manualmente após o deploy, no terminal do Railway.

## 4️⃣ Rodar Migrations pela primeira vez

**DEPOIS** que o app estiver rodando (mesmo com erro de banco), acesse o Terminal no Railway e execute:

```bash
# Testar conexão
php artisan migrate:status

# Rodar migrations
php artisan migrate --force

# (Opcional) Rodar seeders
php artisan db:seed --force
```

## 5️⃣ Criar Usuário Admin

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

## 6️⃣ Executar Seeders

Se quiser popular exercícios e dados iniciais:
```bash
php artisan db:seed --class=ExerciseSeeder
```

## 🔧 Troubleshooting

### Erro 500 (Internal Server Error)
**Causa comum:** APP_KEY não configurada ou porta errada

**Solução:**
1. Verifique se `APP_KEY` está nas variáveis de ambiente
2. **REMOVA** qualquer variável `PORT` que você adicionou manualmente
3. O Railway define `$PORT` automaticamente
4. No terminal do Railway, execute:
```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```
5. Restart o serviço

### Logs não aparecem
**Solução:** Adicione a variável de ambiente:
```env
LOG_CHANNEL=errorlog
```
Depois veja os logs em: Railway > Deployments > View Logs

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
