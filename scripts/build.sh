#!/bin/bash
set -e

echo "🚀 Iniciando build do Sistema Academia..."

echo "📦 Instalando dependências PHP (composer)..."
COMPOSER_ALLOW_SUPERUSER=1 composer install --no-dev --optimize-autoloader --ignore-platform-reqs

echo "🎨 Instalando dependências Node e buildando assets..."
npm install
npm run build

echo "🔑 Verificando APP_KEY..."
if [ -z "$APP_KEY" ]; then
  echo "APP_KEY não encontrada — gerando temporariamente (recomenda-se setar APP_KEY no Railway)"
  php artisan key:generate --force
fi

echo "🔗 Criando link do storage (se já existir, ignora)..."
php artisan storage:link || true

echo "⚡ Limpando caches..."
php artisan config:clear
php artisan cache:clear
php artisan view:clear

echo "⏳ Aguardando banco de dados (se necessário) para rodar migrations..."
MAX_RETRIES=30
SLEEP=5
i=0
while ! php artisan migrate:status > /dev/null 2>&1; do
  if [ "$i" -ge "$MAX_RETRIES" ]; then
    echo "⚠️ Banco não ficou disponível após $((MAX_RETRIES*SLEEP))s. Pulando migrations automáticas."
    break
  fi
  echo "Aguardando DB... ($i/$MAX_RETRIES)"
  i=$((i+1))
  sleep $SLEEP
done

if php artisan migrate:status > /dev/null 2>&1; then
  echo "📊 Rodando migrations..."
  # Gera migration de sessions caso não exista
  php artisan session:table || true
  php artisan migrate --force

  echo "👤 Seed inicial (Admin) - opcional"
  php artisan db:seed --class=AdminUserSeeder --force || true
else
  echo "⚠️ Migrations não foram executadas. Rode-as manualmente quando o DB estiver disponível."
fi

echo "✅ Build concluído com sucesso"