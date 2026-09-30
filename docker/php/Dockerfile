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

# Installation de Composer (copie depuis l'image officielle)
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Installation de Node.js + npm (copie depuis l'image officielle Node 20 LTS)
COPY --from=node:20-slim /usr/local/bin/node /usr/local/bin/node
COPY --from=node:20-slim /usr/local/lib/node_modules /usr/local/lib/node_modules
RUN ln -s /usr/local/lib/node_modules/npm/bin/npm-cli.js /usr/local/bin/npm \
    && ln -s /usr/local/lib/node_modules/npm/bin/npx-cli.js /usr/local/bin/npx

# Vérification des versions installées
RUN composer --version && node --version && npm --version

# Copie la config PHP custom
COPY php.ini /usr/local/etc/php/conf.d/custom.ini

# Création du répertoire de l'application
WORKDIR /var/www/html

# Permissions pour www-data
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80
