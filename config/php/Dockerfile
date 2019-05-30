FROM php:7.3-fpm

LABEL maintainer="Kevin DeCapite"

RUN apt-get -y update

# Build dependencies.
RUN apt-get install -y libzip-dev \
    && apt-get install -y git-core \
    && docker-php-ext-install zip

# App dependencies.
RUN apt-get install -y libicu-dev \
    && apt-get install -y zip \
    && docker-php-ext-install calendar \
    && docker-php-ext-install pcntl \
    && docker-php-ext-install pdo_mysql \
    && docker-php-ext-configure intl \
    && docker-php-ext-install intl

COPY . /srv/app

# Composer.
COPY /config/php/composer.sh /usr/local/bin/composer-installer
RUN chmod +x /usr/local/bin/composer-installer \
    && composer-installer \
    && mv composer.phar /usr/local/bin/composer \
    && chmod +x /usr/local/bin/composer \
    && composer --version

WORKDIR /srv/app
RUN composer install \
    --no-interaction \
    --prefer-dist

# Remove unneeded.
RUN apt-get autoremove -y --purge git-core
RUN apt-get autoremove -y --purge zip
