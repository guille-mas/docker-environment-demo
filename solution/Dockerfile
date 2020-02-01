FROM php:7.4-apache AS server-production
# copy source code into image
COPY --chown=www-data:www-data ./src /var/www
# install composer and its required packages
RUN apt-get update && \
    apt-get install -yq --no-install-recommends curl git zip unzip && \
    curl -sS https://getcomposer.org/installer | \
    php -- --install-dir=/usr/bin/ --filename=composer && \
    # clean apt-get cache files
    apt-get -y autoremove && \
    apt-get clean && \
    rm -rf /var/lib/apt/lists/* && \
    # fix permissions
    mkdir -p /var/www/vendor && \
    chown -R www-data:www-data /var/www
WORKDIR /var/www
USER www-data
RUN composer install --no-cache --no-scripts --prefer-dist --no-autoloader --no-dev && \
    composer dump-autoload --no-dev --classmap-authoritative --optimize
USER root
EXPOSE 80


FROM server-production AS server-development
USER www-data
RUN composer install --no-cache --no-scripts --prefer-dist
USER root

