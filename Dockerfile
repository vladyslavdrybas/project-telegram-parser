# the different stages of this Dockerfile are meant to be built into separate images
# https://docs.docker.com/compose/compose-file/#target

ARG PHP_VERSION=8.0
ARG NGINX_VERSION=1.21

FROM php:${PHP_VERSION}-fpm-alpine AS app_php

MAINTAINER Vladyslav Drybas <https://github.com/vladyslavdrybas>

RUN apk update \
 && apk add \
	bash \
	git \
	make \
	wget \
	file \
    curl \
    gcc \
	g++ \
	icu-dev \
    autoconf \
	freetype \
	freetype-dev \
    php8-intl \
	php8-pecl-apcu \
    php8-json

RUN docker-php-ext-install bcmath pcntl sockets


RUN echo "UTC" > /etc/timezone

COPY --from=composer:2.1.14 /usr/bin/composer /usr/bin/composer
COPY docker/php/php.ini /usr/local/etc/php/php.ini
COPY docker/php/php-cli.ini /usr/local/etc/php/php-cli.ini

ENV COMPOSER_ALLOW_SUPERUSER=1
RUN set -eux; \
	composer clear-cache
ENV PATH="${PATH}:/root/.composer/vendor/bin"

WORKDIR /srv/app

# prevent the reinstallation of vendors at every changes in the source code
COPY composer.* ./
#RUN set -eux; \
#	composer install --prefer-dist --no-autoloader --no-scripts --no-progress; \
#	composer clear-cache \

# copy only specifically what we need
COPY .env* ./
COPY bin bin/
COPY config config/
COPY public public/
COPY src src/

RUN composer dump-autoload --optimize

COPY docker/php/docker-entrypoint.sh /usr/local/bin/docker-entrypoint
RUN chmod +x /usr/local/bin/docker-entrypoint

ENTRYPOINT ["docker-entrypoint"]
CMD ["php-fpm"]

FROM nginx:${NGINX_VERSION}-alpine AS app_nginx

COPY docker/nginx/conf.d/default.conf /etc/nginx/conf.d/

WORKDIR /srv/app
