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
	rabbitmq-c-dev \
	icu-dev \
    autoconf \
    ffmpeg \
	libjpeg-turbo-dev \
	libpng-dev \
	libjpeg-turbo-dev \
	freetype \
	freetype-dev \
	imagemagick \
	imagemagick-dev \
	imagemagick-libs \
    postgresql-dev \
    php8-intl \
	php8-pecl-apcu \
    php8-pecl-amqp \
    php8-pecl-redis \
    php8-json \
    php8-xml \
    php8-gd

RUN docker-php-ext-configure gd --with-jpeg --with-freetype
RUN docker-php-ext-install gd pdo pdo_pgsql pdo_mysql iconv bcmath pcntl sockets

RUN pecl install redis-5.3.4 && docker-php-ext-enable redis
RUN pecl install amqp && docker-php-ext-enable amqp
RUN pecl install imagick && docker-php-ext-enable imagick

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

# copy only specifically what we need
COPY .env* ./
COPY bin bin/
COPY config config/
COPY public public/
COPY src src/
COPY templates templates/

RUN composer dump-autoload --optimize

COPY docker/php/docker-entrypoint.sh /usr/local/bin/docker-entrypoint
RUN chmod +x /usr/local/bin/docker-entrypoint

ENTRYPOINT ["docker-entrypoint"]
CMD ["php-fpm"]

FROM nginx:${NGINX_VERSION}-alpine AS app_nginx

COPY docker/nginx/conf.d/default.conf /etc/nginx/conf.d/

WORKDIR /srv/app
