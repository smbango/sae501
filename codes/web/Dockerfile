FROM php:8.2-apache

RUN apt-get update && apt-get install -y \
	libsqlite3-dev \
	libpng-dev \
	libjpeg-dev \
	libfreetype6-dev \
	&& docker-php-ext-configure gd --with-freetype --with-jpeg \
	&& docker-php-ext-install -j$(nproc) pdo_sqlite gd

RUN a2enmod rewrite
