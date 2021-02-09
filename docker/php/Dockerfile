ARG PHP_VER=8.0.3

FROM php:${PHP_VER}-cli-alpine as builder
COPY composer.json /
RUN apk add curl
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/bin --filename=composer --quiet;
RUN docker-php-ext-install sockets
ENV ROADRUNNER_VERSION v2.0.1
RUN mkdir /tmp/rr \
  ; cd /tmp/rr \
  ; cp /composer.json composer.json \
  ; composer require spiral/roadrunner:${ROADRUNNER_VERSION} nyholm/psr7
RUN (cd /tmp/rr; vendor/bin/rr get-binary)


FROM php:${PHP_VER}-cli-alpine
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
        unzip \
        yaml \
        ;\
    apk add -t .build-deps \
        autoconf build-base pkgconfig m4 perl autoconf dpkg-dev dpkg re2c util-linux-dev \
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
    mkdir /tmp/amqp ;\
        curl -L https://github.com/php-amqp/php-amqp/archive/master.tar.gz | tar -xzC /tmp/amqp --strip-components=1 ;\
        unzip /tmp/amqp.zip -d /tmp/amqp;\
        cd /tmp/amqp; \
        phpize --clean ;\
        phpize;\
        ./configure;\
        make;\
        make install; \
    pickle install apcu ;\
    pickle install igbinary-3.2.1 ;\
    pickle install redis ;\
    pickle install uuid-1.2.0 ;\
    pickle install yaml ;\
    pickle install xdebug-3.0.3 ;\
    echo "extension=apcu.so" > /usr/local/etc/php/conf.d/docker-php-ext-apcu.ini ; \
    echo "extension=amqp.so" > /usr/local/etc/php/conf.d/docker-php-ext-amqp.ini ; \
    echo "extension=igbinary.so" > /usr/local/etc/php/conf.d/docker-php-ext-igbinary.ini ; \
    echo "extension=redis.so" > /usr/local/etc/php/conf.d/docker-php-ext-redis.ini ; \
    echo "zend_extension=xdebug.so" > /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini ;\
    echo "extension=uuid.so" > /usr/local/etc/php/conf.d/docker-php-ext-uuid.ini ; \
    echo "extension=yaml.so" > /usr/local/etc/php/conf.d/docker-php-ext-yaml.ini ; \
    cd / ;\
    rm -rf /tmp/* ;\
    rm -rf /var/cache/apk/* ;\
    docker-php-source delete;\
    apk del --purge .build-deps

RUN apk add --update --no-cache git
RUN apk add --update --no-cache exiftool pngquant imagemagick

COPY --from=builder /tmp/rr/rr /usr/local/bin/rr
RUN chmod 755 /usr/local/bin/rr
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
