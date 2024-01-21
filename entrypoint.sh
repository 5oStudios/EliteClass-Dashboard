#!/bin/bash

# Run database migrations
php artisan migrate --force --no-interaction

# Run the storage:link command
php artisan storage:link

# Clear cache and optimize
php artisan optimize:clear
php artisan config:clear
php artisan cache:clear

php artisan passport:keys

# Start PHP-FPM server
exec php-fpm