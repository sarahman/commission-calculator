FROM php:7.0-fpm

ARG DEBIAN_FRONTEND="noninteractive"

RUN apt-get update && apt-get install -y curl git zlib1g-dev

RUN docker-php-ext-install zip

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
