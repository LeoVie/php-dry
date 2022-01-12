FROM php:8.1.1-fpm

COPY bin/console /var/www/bin/console
COPY config /var/www/config
COPY generated /var/www/generated
COPY src /var/www/src
COPY .env /var/www/.env
COPY composer.json /var/www/composer.json
COPY composer.lock /var/www/composer.lock
COPY symfony.lock /var/www/symfony.lock
COPY vendor /var/www/vendor
COPY public /var/www/public

RUN apt-get update && apt-get install -y git

COPY docker/project/install_composer.sh /var/install_composer.sh
RUN chmod +x /var/install_composer.sh && /var/install_composer.sh
RUN cd /var/www && export COMPOSER_DISCARD_CHANGES=true && php /var/composer.phar install --no-dev

ENTRYPOINT ["/var/www/bin/console", "php-cd:check", "/project"]