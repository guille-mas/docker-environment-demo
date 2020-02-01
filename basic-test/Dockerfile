FROM php:7.4-apache AS basic-server-production
# copy source code into image
COPY --chown=www-data:www-data ./src/server /var/www
EXPOSE 80

FROM basic-server-production AS basic-server-development
RUN apt-get update && \
    apt-get install -yq --no-install-recommends curl git zip unzip && \
    usermod -u 1000 www-data && \
    groupmod -g 1000 www-data && \
    curl -sS https://getcomposer.org/installer | \
    php -- --install-dir=/usr/bin/ --filename=composer && \
    apt-get -y autoremove && \
    apt-get clean && \
    rm -rf /var/lib/apt/lists/* && \
    chown -R www-data /var/www && \
    mkdir -p /var/www/vendor && \
    composer install -d /var/www --no-cache --no-scripts --prefer-dist
WORKDIR /var/www/html
