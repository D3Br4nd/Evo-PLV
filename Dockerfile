FROM dunglas/frankenphp:php8.4

RUN install-php-extensions \
    pdo_pgsql \
    redis \
    bcmath \
    intl \
    zip \
    pcntl

WORKDIR /app
