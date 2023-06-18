FROM php:8.1-fpm-alpine

# Arguments defined in docker-compose.yml
ARG user
ARG uid

RUN apk --update add wget \
  curl \
  libxml2-dev

COPY --from=composer/composer:2.3.10-bin /composer /usr/bin/composer

RUN docker-php-ext-install mysqli pdo pdo_mysql xml sockets

RUN rm /var/cache/apk/* && \
    mkdir -p /var/www

# Create system user to run Composer and Artisan Commands
RUN addgroup -g $uid -S $user && \
    adduser -u $uid -S $user -G $user
RUN chown -R $user:$user /var/www

USER $user