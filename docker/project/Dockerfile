FROM php:8.1.1-alpine

COPY php-dry /var/www/php-dry
COPY config /var/www/config
COPY generated /var/www/generated
COPY src /var/www/src
COPY .env /var/www/.env
COPY composer.json /var/www/composer.json
COPY vendor /var/www/vendor
COPY VERSION /var/www/VERSION
COPY tools /var/www/tools
COPY templates /var/www/templates
COPY resources /var/www/resources

COPY docker/project/php.ini "$PHP_INI_DIR/php.ini"

ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/

RUN chmod +x /usr/local/bin/install-php-extensions && \
    install-php-extensions intl

ENTRYPOINT [ \
    "php", "-d", "memory_limit=-1", \
    "/var/www/php-dry" \
]