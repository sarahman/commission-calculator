FROM php:7.4-alpine

ARG DEBIAN_FRONTEND="noninteractive"

WORKDIR /var/www/html

RUN apk update && apk add curl git zip unzip

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
