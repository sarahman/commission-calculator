FROM php:7.0-fpm

LABEL maintainer="Syed Abidur Rahman <aabid048@gmail.com>"

ARG DEBIAN_FRONTEND="noninteractive"

RUN apt-get update && apt-get install -y curl git unzip zip

RUN docker-php-ext-install bcmath

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
