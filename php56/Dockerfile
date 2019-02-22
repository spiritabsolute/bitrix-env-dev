FROM php:5.6-fpm

ARG INTERFACE="0.0.0.0"

COPY ./custom.ini $PHP_INI_DIR/conf.d/custom.ini
COPY ./opcache.ini $PHP_INI_DIR/conf.d/opcache.ini
COPY ./xdebug.ini $PHP_INI_DIR/conf.d/xdebug.ini

ENV INTERFACE $INTERFACE

RUN apt-get update && apt-get install -y \
	libfreetype6-dev \
	libjpeg62-turbo-dev \
	libpng-dev \
	libxml2-dev \
&& docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \
&& docker-php-ext-configure opcache --enable-opcache

RUN docker-php-ext-install xml zip gd mysqli opcache \
&& pecl install xdebug-2.5.5 && docker-php-ext-enable xdebug \
&& sed -i "1 a xdebug.remote_host="$INTERFACE $PHP_INI_DIR/conf.d/xdebug.ini

RUN usermod -u 1000 www-data

EXPOSE 9000