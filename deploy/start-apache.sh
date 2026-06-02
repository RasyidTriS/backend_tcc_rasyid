#!/usr/bin/env bash
set -e

php artisan config:clear --no-interaction || true
php artisan route:clear --no-interaction || true
php artisan view:clear --no-interaction || true

exec apache2-foreground
