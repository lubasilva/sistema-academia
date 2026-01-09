#!/bin/bash
set -e

echo "🚀 Iniciando build do Sistema Academia..."

echo "📦 Instalando dependências PHP (composer)..."
COMPOSER_ALLOW_SUPERUSER=1 composer install --no-dev --optimize-autoloader --ignore-platform-reqs --no-scripts

echo "🔑 Gerando APP_KEY se necessário..."
if [ -z "$APP_KEY" ]; then
  php artisan key:generate --force
fi

echo "🔗 Limpando caches..."
php artisan config:clear
php artisan view:clear
php artisan route:clear

echo "✅ Build concluído em $(date +%s) segundos"