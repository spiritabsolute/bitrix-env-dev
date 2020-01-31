FROM composer AS composer

RUN composer require "phpunit/phpunit:^8" \
  --no-interaction \
  --no-plugins \
  --no-scripts \
  --prefer-dist

FROM php:7.2-fpm-stretch

ARG INTERFACE="0.0.0.0"

COPY ./custom.ini $PHP_INI_DIR/conf.d/custom.ini
COPY ./opcache.ini $PHP_INI_DIR/conf.d/opcache.ini
COPY ./xdebug.ini $PHP_INI_DIR/conf.d/xdebug.ini
COPY ./sendmail.ini $PHP_INI_DIR/conf.d/sendmail.ini

COPY ./ssmtp.conf /etc/ssmtp/ssmtp.conf

COPY --from=composer /app/vendor /vendor

COPY ./phpunit.xml /phpunit.xml
COPY ./.bootstrap.php /.bootstrap.php

ENV INTERFACE $INTERFACE

RUN apt-get update && apt-get install -y ssmtp \
zip libzip-dev \
libfreetype6-dev \
libjpeg62-turbo-dev \
libpng-dev \
libxml2-dev \
&& docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \
&& docker-php-ext-configure opcache --enable-opcache \
&& docker-php-ext-configure zip --with-libzip

RUN docker-php-ext-install xml zip gd mysqli opcache \
&& pecl install xdebug && docker-php-ext-enable xdebug \
&& sed -i "1 a xdebug.remote_host="$INTERFACE $PHP_INI_DIR/conf.d/xdebug.ini

RUN usermod -u 1000 www-data

EXPOSE 9000
