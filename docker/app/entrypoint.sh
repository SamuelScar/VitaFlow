#!/bin/sh
set -e

if [ -d /var/www/html/storage ] && [ -d /var/www/html/bootstrap/cache ]; then
    chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
    chmod -R ug+rwX /var/www/html/storage /var/www/html/bootstrap/cache
fi

if [ "$RUN_VITE_DEV" = "true" ] && [ -f /var/www/html/package.json ]; then
    npm run dev -- --host 0.0.0.0 &
fi

exec "$@"
