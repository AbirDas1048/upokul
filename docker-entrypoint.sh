#!/bin/bash
set -e

echo "========================================"
echo "  Starting Upokul Travels & Holidays"
echo "========================================"

# ── Wait for MySQL to be ready ───────────────────────────────────────────────
echo "Waiting for MySQL to be ready..."
until php -r "
    try {
        new PDO(
            'mysql:host=' . getenv('DB_HOST') . ';port=' . getenv('DB_PORT') . ';dbname=' . getenv('DB_DATABASE'),
            getenv('DB_USERNAME'),
            getenv('DB_PASSWORD')
        );
        echo 'connected';
    } catch (Exception \$e) {
        exit(1);
    }
" 2>/dev/null | grep -q "connected"; do
    echo "  MySQL not ready yet. Retrying in 3 seconds..."
    sleep 3
done
echo "  MySQL is ready!"

# ── Generate APP_KEY if missing ──────────────────────────────────────────────
if [ -z "$APP_KEY" ]; then
    echo "Generating APP_KEY..."
    php artisan key:generate --force
fi

# ── Run database migrations ──────────────────────────────────────────────────
echo "Running database migrations..."
php artisan migrate --force

# ── Cache config for performance ────────────────────────────────────────────
echo "Caching config and routes..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# ── Fix storage permissions ──────────────────────────────────────────────────
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

echo "========================================"
echo "  App is ready at http://localhost:8080"
echo "========================================"

# Start Apache
exec "$@"
