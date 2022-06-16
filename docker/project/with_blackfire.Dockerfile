FROM php:8.1.1-fpm

ARG CLIENT_ID
ENV BLACKFIRE_CLIENT_ID=$CLIENT_ID
ARG CLIENT_TOKEN
ENV BLACKFIRE_CLIENT_TOKEN=$CLIENT_TOKEN
ARG SERVER_ID
ENV BLACKFIRE_SERVER_ID=$SERVER_ID
ARG SERVER_TOKEN
ENV BLACKFIRE_SERVER_TOKEN=$SERVER_TOKEN

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

RUN mkdir -p /tmp/blackfire \
    && architecture=$(uname -m) \
    && curl -A "Docker" -L https://blackfire.io/api/v1/releases/cli/linux/$architecture | tar zxp -C /tmp/blackfire \
    && mv /tmp/blackfire/blackfire /usr/bin/blackfire \
    && rm -Rf /tmp/blackfire

RUN blackfire php:install

ENTRYPOINT [ \
    "blackfire", "run", "--ignore-exit-status", "php", "-d", "memory_limit=-1", \
    "/var/www/php-dry" \
]