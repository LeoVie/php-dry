FROM php:8.1.1-fpm

COPY bin/console /var/www/bin/console
COPY config /var/www/config
COPY generated /var/www/generated
COPY src /var/www/src
COPY .env /var/www/.env
COPY composer.json /var/www/composer.json
COPY vendor /var/www/vendor
COPY public /var/www/public

ENTRYPOINT ["php", "-d", "memory_limit=-1", "/var/www/bin/console", "php-cd:check", "/project"]