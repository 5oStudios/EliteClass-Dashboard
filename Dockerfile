FROM php:8.2-fpm

# Arguments defined in docker-compose.yml
ARG USERNAME=studios
ARG UID=1001
ENV TZ=Asia/Kuwait

# Install system dependencies
RUN apt update && apt install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip \
    && docker-php-ext-install mysqli pdo_mysql mbstring exif pcntl bcmath gd zip \
    && apt clean  \
    && rm -rf /var/lib/apt/lists/*

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY . .

RUN composer update && \
    composer install --no-progress && composer dump-autoload

# Create system user to run Composer and Artisan Commands
RUN useradd -G www-data,root -u $UID -d /home/$USERNAME $USERNAME \
    && rm -r storage/app/public  \
    && ln -s /app/storage/public storage/app/  \
    && chmod +x entrypoint.sh  \
    && chown -R $USERNAME:$USERNAME .

USER $USERNAME

EXPOSE 9000

CMD ["php-fpm"]

ENTRYPOINT ["/var/www/html/entrypoint.sh"]