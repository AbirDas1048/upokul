#!/bin/bash
set -e

echo "========================================"
echo "  Starting Upokul dev environment"
echo "========================================"

if [ ! -f "vendor/autoload.php" ]; then
    echo "Installing composer dependencies..."
    composer install --no-interaction --optimize-autoloader
fi

if ! grep -q "^APP_KEY=base64" .env.docker 2>/dev/null; then
    echo "Generating APP_KEY..."
    php artisan key:generate --force
fi

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

echo "Running database migrations..."
php artisan migrate --force

chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

echo "========================================"
echo "  App is ready at http://localhost:8080"
echo "========================================"

exec "$@"
