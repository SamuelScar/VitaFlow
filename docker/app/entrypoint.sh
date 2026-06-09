#!/bin/sh
set -e

log() {
    printf '[app] %s\n' "$1"
}

log "Ajustando permissoes do Laravel..."

if [ -d /var/www/html/storage ] && [ -d /var/www/html/bootstrap/cache ]; then
    chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
    chmod -R ug+rwX /var/www/html/storage /var/www/html/bootstrap/cache
fi

if [ -f /var/www/html/composer.json ]; then
    if [ ! -f /var/www/html/vendor/autoload.php ] \
        || [ /var/www/html/composer.lock -nt /var/www/html/vendor/autoload.php ]; then
        log "Instalando dependencias do Composer..."
        composer install --no-interaction --prefer-dist
    else
        log "Dependencias do Composer ja estao atualizadas."
    fi

    log "Executando migrations..."
    php artisan migrate --force
fi

if [ "$RUN_VITE_DEV" = "true" ] && [ -f /var/www/html/package.json ]; then
    if [ ! -x /var/www/html/node_modules/.bin/vite ] \
        || [ /var/www/html/package-lock.json -nt /var/www/html/node_modules/.package-lock.json ]; then
        log "Instalando dependencias do npm..."
        npm ci --no-audit --no-fund
    else
        log "Dependencias do npm ja estao atualizadas."
    fi

    log "Iniciando Vite..."
    npm run dev -- --host 0.0.0.0 &
else
    log "Vite automatico desabilitado."
fi

log "Iniciando servidor Apache..."
exec "$@"
