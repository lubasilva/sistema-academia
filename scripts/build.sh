#!/bin/bash
set -e

echo "🚀 Iniciando build do Sistema Academia..."

echo "📦 Instalando dependências PHP (composer)..."
composer install --no-dev --optimize-autoloader

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

echo "✅ Build concluído com sucesso"