#!/bin/sh
set -e

if [ -d /var/www/html/storage ] && [ -d /var/www/html/bootstrap/cache ]; then
    chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
    chmod -R ug+rwX /var/www/html/storage /var/www/html/bootstrap/cache
fi

exec "$@"
