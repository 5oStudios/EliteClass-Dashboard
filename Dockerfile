FROM php:8.2-fpm

# Arguments defined in docker-compose.yml
ARG user=studios
ARG uid=1001

ENV TZ=Asia/Kuwait

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd soap zip

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

COPY . .

RUN composer install --no-interaction --prefer-dist --optimize-autoloader && composer dump-autoload

#RUN composer update && \
#    composer install --no-progress && composer dump-autoload

# Create system user to run Composer and Artisan Commands
RUN useradd -G www-data,root -u $uid -d /home/$user $user
RUN mkdir -p /home/$user/.composer && \
    rm -r storage/app/public && \
    ln -s /app/storage/public storage/app/ && \
    chmod +x entrypoint.sh && \
    chown -R $user:$user /home/$user && \
    chown -R $user:$user .

USER $user

EXPOSE 9000

ENTRYPOINT ["/var/www/entrypoint.sh"]