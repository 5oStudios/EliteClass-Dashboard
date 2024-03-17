# syntax=docker/dockerfile:1.4

FROM mlocati/php-extension-installer:latest AS php_extension_installer

FROM php:8.2-fpm-alpine

ARG USERNAME=studios
ARG UID=1001
ARG GROUP_NAME=studios
ARG GID=1001
ARG DIR_PATH=/var/www/html

ENV TZ=Asia/Kuwait

# php extensions installer: https://github.com/mlocati/docker-php-extension-installer
COPY --from=php_extension_installer --link /usr/bin/install-php-extensions /usr/local/bin/

RUN apk update && apk add --no-cache python3 py3-pip openrc supervisor tzdata;

RUN set -eux; \
    install-php-extensions intl zip apcu opcache pdo pdo_mysql;

COPY queue-worker.ini /etc/supervisor.d/queue-worker.ini

RUN  sed -i 's/^\(\[supervisord\]\)$/\1\nnodaemon=true/' /etc/supervisord.conf;

# Define mountable directories.
VOLUME ["/etc/supervisor/conf.d"]
VOLUME ["/usr/share/nginx/html"]

# Define working directory.
WORKDIR /etc/supervisor/conf.d

RUN addgroup --gid ${GID} --system ${GROUP_NAME} \
    && adduser --system --ingroup ${GROUP_NAME} --shell /bin/bash --uid ${UID} --disabled-password ${USERNAME};

# Define default command.
ENTRYPOINT ["supervisord", "-c", "/etc/supervisord.conf"]