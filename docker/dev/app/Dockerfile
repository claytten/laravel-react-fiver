FROM php:8.1-fpm-alpine

RUN apk --update add wget \
  curl \
  git \
  build-base \
  libxml2-dev \
  imagemagick-dev \
  pcre-dev \
  libtool \
  make \
  autoconf \
  g++ 

COPY --from=composer/composer:2.3.10-bin /composer /usr/bin/composer

RUN docker-php-ext-install mysqli pdo pdo_mysql xml
RUN pecl channel-update pecl.php.net \
    && pecl install imagick \
    && docker-php-ext-enable imagick

RUN rm /var/cache/apk/* && \
    mkdir -p /var/www