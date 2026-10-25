FROM php:8.2-apache

# Installation des dépendances GD (captcha) et de l'extension MariaDB/MySQL
RUN apt-get update && apt-get install -y \
	libpng-dev \
	libjpeg-dev \
	libfreetype6-dev \
	&& docker-php-ext-configure gd --with-freetype --with-jpeg \
	&& docker-php-ext-install -j$(nproc) pdo_mysql gd

RUN a2enmod rewrite
