ARG DOCKER_PHP_VERSION=8.2-alpine
FROM php:${DOCKER_PHP_VERSION}

COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin

RUN set -eux; \
    install-php-extensions \
        pcov \
        xdebug \
    ;

COPY --from=composer/composer:2-bin /composer /usr/local/bin
