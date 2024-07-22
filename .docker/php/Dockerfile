FROM php:8.3-fpm-alpine

RUN apk update && apk add --no-cache \
    autoconf \
    gcc \
    g++ \
    make \
    pcre-dev \
    zlib-dev \
    file \
    bash \
    linux-headers \
    git \
    curl \
    libpng-dev \
    oniguruma-dev \
    libxml2-dev \
    libzip-dev \
    icu-dev \
    libc6-compat \
    zip \
    unzip \
    supervisor \
    libtool \
    mariadb-client \
    nodejs \
    npm

RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip intl

COPY .docker/php/php.ini /usr/local/etc/php/conf.d/php.ini

VOLUME /root/composer

ENV COMPOSER_HOME /root/composer

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && composer selfupdate

WORKDIR /tmp

ENV NODE_VERSION 18.20.4
ENV NVM_DIR /root/.nvm
ENV PATH $NVM_DIR/versions/node/v$NODE_VERSION/bin:$PATH

RUN curl -fsSL https://nodejs.org/dist/v$NODE_VERSION/node-v$NODE_VERSION-linux-x64.tar.xz | tar -xJf - -C /usr/local --strip-components=1

# Usunięcie niepotrzebnych plików
RUN rm -rf /var/cache/apk/*

WORKDIR /var/www/html
