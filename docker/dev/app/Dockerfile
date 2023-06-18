FROM php:8.1-fpm-alpine

RUN apk --update add wget \
  curl \
  libxml2-dev

COPY --from=composer/composer:2.3.10-bin /composer /usr/bin/composer

RUN docker-php-ext-install mysqli pdo pdo_mysql xml sockets

RUN rm /var/cache/apk/* && \
    mkdir -p /var/www