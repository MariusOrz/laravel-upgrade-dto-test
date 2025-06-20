#!/bin/bash
set -ex

echo "Starting entrypoint script..."

if [[ $APP_ENV == "production" ]]; then
    echo "Production environment detected."

    echo "Optimizing composer & autoloader.."
    composer install --optimize-autoloader --no-dev

    echo "Starting migrations.."
    php artisan migrate --force

    echo "Starting permissions seeding.."
    php artisan db:seed --class="App\Domain\Backoffice\Permission\v1\Database\Seeders\PermissionTableSeeder" --force

    echo "Starting App Optimizing.."
    php artisan optimize
fi

/usr/sbin/php-fpm83
