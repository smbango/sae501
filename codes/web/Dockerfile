FROM php:8.2-apache

RUN apt-get update && apt-get install -y sqlite3 libsqlite3-dev \
    && docker-php-ext-install pdo pdo_sqlite \
    && rm -rf /var/lib/apt/lists/*

COPY code/ /var/www/html/

RUN mkdir -p /var/data && chmod 777 /var/data

EXPOSE 80
