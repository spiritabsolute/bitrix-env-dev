FROM php:7.2.14-fpm

RUN apt-get update && apt-get install -y \
	libfreetype6-dev \
	libjpeg62-turbo-dev \
	libpng-dev \
&& docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \
&& docker-php-ext-configure opcache --enable-opcache

RUN docker-php-ext-install gd mysqli opcache \
&& pecl install xdebug-2.6.0 && docker-php-ext-enable xdebug

COPY ./custom.ini $PHP_INI_DIR/conf.d/custom.ini
COPY ./opcache.ini $PHP_INI_DIR/conf.d/opcache.ini
COPY ./xdebug.ini $PHP_INI_DIR/conf.d/xdebug.ini

RUN usermod -u 1000 www-data

EXPOSE 9000