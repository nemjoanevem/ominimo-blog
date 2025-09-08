FROM php:8.3-fpm-alpine

ARG TZ=Europe/Budapest
ENV TZ=${TZ} \
    COMPOSER_ALLOW_SUPERUSER=1

# System deps (Alpine): postgresql-dev az, ami kell pdo_pgsql-hez
RUN apk add --no-cache \
    bash \
    git \
    unzip \
    icu-dev \
    oniguruma-dev \
    postgresql-dev

# PHP extensions
RUN docker-php-ext-install pdo pdo_pgsql intl

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Non-root user
RUN addgroup -g 1000 app && adduser -G app -g app -s /bin/sh -D app
USER app
