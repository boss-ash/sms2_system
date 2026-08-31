FROM php:8.2-apache

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        libcurl4-openssl-dev \
        libonig-dev \
    && docker-php-ext-install curl mbstring mysqli pdo pdo_mysql \
    && a2enmod headers rewrite \
    && rm -rf /var/lib/apt/lists/*

# Build this Dockerfile with the repository root as the Docker context:
# docker build -f modules/crad/Dockerfile .
COPY . /var/www/html/

WORKDIR /var/www/html/

RUN test -f /var/www/html/index.php \
    && test -f /var/www/html/includes/authentication.php \
    && test -f /var/www/html/modules/crad/index.php

ENV PORT=8000

CMD ["sh", "-c", "sed -i \"s/^Listen .*/Listen ${PORT:-8000}/\" /etc/apache2/ports.conf && sed -i \"s/<VirtualHost \\*:[0-9]*>/<VirtualHost *:${PORT:-8000}>/\" /etc/apache2/sites-available/000-default.conf && apache2-foreground"]
