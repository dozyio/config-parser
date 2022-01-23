ARG PHP_VERSION=7.4-cli-alpine
ARG COMPOSER_VERSION=2.2.4
ARG ALPINE_VERSION=3.15

#src layer
FROM alpine:${ALPINE_VERSION} as src_layer
WORKDIR /app
COPY composer.json composer.lock README.md phpunit.xml .
COPY src/ ./src/
COPY tests/ ./tests/

#composer layer
FROM composer:${COMPOSER_VERSION} as composer_layer
WORKDIR /app
COPY --from=src_layer /app .
RUN mkdir /root/.composer
RUN composer install \
    --ignore-platform-reqs \
    --no-interaction \
    --no-plugins \
    --no-scripts \
    --prefer-dist

#php layer
FROM php:${PHP_VERSION} as php_layer
WORKDIR /app
COPY --from=src_layer /app .
COPY --from=composer_layer /app .

CMD ["vendor/bin/phpunit"]
