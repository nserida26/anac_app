FROM php:8.1-apache

# Installation des dépendances système et extensions PHP
RUN apt-get update && apt-get install -y \
    git \
    curl \
    zip \
    unzip \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    libicu-dev \
    libpq-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libcurl4-openssl-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install \
        pdo_mysql \
        mbstring \
        xml \
        bcmath \
        curl \
        gd \
        zip \
        intl \
        exif \
        soap \
        opcache \
    && a2enmod rewrite \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Copie la config PHP custom
COPY php.ini /usr/local/etc/php/conf.d/custom.ini

# Création du répertoire de l'application
WORKDIR /var/www/html

# Permissions pour www-data
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80
