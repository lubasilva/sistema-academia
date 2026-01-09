release: sh -c 'php artisan migrate --force 2>/dev/null || true' && sh -c 'php artisan db:seed --force 2>/dev/null || true'
web: php artisan storage:link 2>/dev/null || true && php artisan optimize && php -S 0.0.0.0:${PORT:-8000} -t public
