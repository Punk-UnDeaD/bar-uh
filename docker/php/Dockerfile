ARG PHP_VER=8.1.3
ARG ALPINE_VER=3.15

FROM php:${PHP_VER}-cli-alpine${ALPINE_VER} as rr
ARG ROADRUNNER_VERSION=v2.8.2
RUN apk add curl \
  ; curl -sS https://getcomposer.org/installer | php -- --install-dir=/bin --filename=composer --quiet \
  ; echo "{}" > composer.json \
  ; composer require --ignore-platform-reqs spiral/roadrunner:${ROADRUNNER_VERSION} \
  ; vendor/bin/rr get-binary -f ${ROADRUNNER_VERSION} -s \
  ; mv rr /usr/local/bin/rr \
  ; chmod 755 /usr/local/bin/rr \
  ; rm -rf vendor \
  ; rm composer.* ; rm .rr.yaml

FROM php:${PHP_VER}-cli-alpine${ALPINE_VER}
RUN apk add --update --no-cache git
RUN apk add --update --no-cache exiftool pngquant imagemagick
RUN apk update ;\
    apk add -t .run-deps \
        bash \
        curl \
        gzip \
        icu-libs \
        libzip \
        libuuid \
        nano \
        patch \
        postgresql-client \
        rabbitmq-c \
        tar \
        unzip

RUN apk add -t .build-deps \
        autoconf build-base pkgconfig m4 perl autoconf dpkg-dev dpkg re2c util-linux-dev \
        make \
        icu-dev \
        libzip-dev \
        postgresql-dev \
        rabbitmq-c-dev \
        yaml-dev; \
    docker-php-ext-install \
        intl \
        opcache \
        pdo_pgsql \
        pgsql \
        sockets \
        zip ;\
    curl -sS https://getcomposer.org/installer | php -- --install-dir=/bin --filename=composer --quiet;\
    curl -L https://github.com/FriendsOfPHP/pickle/releases/latest/download/pickle.phar -o /usr/local/bin/pickle;\
    chmod +x /usr/local/bin/pickle ;\
    pickle install amqp-1.11.0 ;\
    pickle install apcu-5.1.21  ;\
    pickle install igbinary-3.2.7 ;\
    pickle install redis-5.3.6  ;\
    pickle install uuid-1.2.0 ;\
    pickle install xdebug-3.1.3 ;\
    echo "extension=amqp.so" > /usr/local/etc/php/conf.d/docker-php-ext-amqp.ini ; \
    echo "extension=apcu.so" > /usr/local/etc/php/conf.d/docker-php-ext-apcu.ini ; \
    echo "extension=igbinary.so" > /usr/local/etc/php/conf.d/docker-php-ext-igbinary.ini ; \
    echo "extension=redis.so" > /usr/local/etc/php/conf.d/docker-php-ext-redis.ini ; \
    echo "extension=uuid.so" > /usr/local/etc/php/conf.d/docker-php-ext-uuid.ini ; \
    cd / ;\
    rm -rf /tmp/* ;\
    rm -rf /var/cache/apk/* ;\
    docker-php-source delete;\
    apk del --purge .build-deps

COPY --from=rr /usr/local/bin/rr /usr/local/bin/rr
WORKDIR /var/www/html
EXPOSE 8080

COPY ./worker.sh ./stop.sh /

RUN apk add --no-cache shadow

ARG UID=1000
ARG GID=1000
RUN usermod -u ${UID} www-data \
  && groupmod -g ${UID} www-data

USER www-data
ENV PATH="./vendor/bin:./bin:${PATH}"
CMD ["/usr/local/bin/rr", "serve", "-c", ".rr.yaml"]
