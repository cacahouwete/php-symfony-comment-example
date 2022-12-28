#######################################################
####################### BASE ##########################
#######################################################
FROM php:8.2-fpm AS base

ENV APPIZE_DEPS \
    wget \
    libxml2-dev \
    libgmp-dev \
    libicu-dev \
    libpq-dev \
    libzip-dev

RUN apt-get update && apt-get install  -y \
    unzip \
    supervisor \
    nginx \
    gosu \
    librabbitmq4 \
    libpq5 \
    libzip4 \
    $APPIZE_DEPS \
    $PHPIZE_DEPS && \
    apt-get clean && \
    rm -rf /var/lib/apt/lists/* && \
    mkdir -p /usr/src/php/ext/apcu && \
    curl -fsSL https://pecl.php.net/get/apcu | tar xvz -C "/usr/src/php/ext/apcu" --strip 1 && \
    docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql  && \
    docker-php-ext-configure zip && \
    docker-php-ext-install -j$(nproc) zip intl pdo_pgsql bcmath apcu gmp soap && \
    docker-php-ext-enable opcache && \
    apt-get purge -y --auto-remove $APPIZE_DEPS $PHPIZE_DEPS && \
    docker-php-source delete

RUN rm -rf /home && \
    addgroup bar && \
    adduser --home /home foo && \
    adduser foo bar

COPY docker/nginx/nginx.conf /etc/nginx/nginx.conf
COPY docker/nginx/symfony.conf /etc/nginx/symfony.conf

ADD docker/entrypoint.sh /entrypoint

WORKDIR /srv

COPY docker/php/pool.conf.ini $PHP_INI_DIR/../php-fpm.d/www.conf
RUN chmod 644 $PHP_INI_DIR/../php-fpm.d/www.conf

ENTRYPOINT ["/entrypoint"]
EXPOSE 80 443 8080

############################
######### BASE-DEV #########
############################

FROM base AS base-dev

RUN set -eux && \
    apt-get update && apt-get install -y gnupg2 \
    $APPIZE_DEPS \
    $PHPIZE_DEPS && \
    apt-get clean && \
    rm -rf /var/lib/apt/lists/* && \
    mkdir -p /usr/src/php/ext/pcov && curl -fsSL https://pecl.php.net/get/pcov | tar xvz -C "/usr/src/php/ext/pcov" --strip 1 && \
    docker-php-ext-install -j$(nproc) pcov && \
    rm $PHP_INI_DIR/conf.d/docker-php-ext-pcov.ini && \
    apt-get purge -y --auto-remove $APPIZE_DEPS $PHPIZE_DEPS && \
    docker-php-source delete

COPY docker/php/php.dev.ini $PHP_INI_DIR/conf.d/php.ini
RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini" && \
    chmod 644 $PHP_INI_DIR/conf.d/php.ini

ADD https://raw.githubusercontent.com/vishnubob/wait-for-it/master/wait-for-it.sh /usr/local/bin/wait-for-it
RUN chmod +x /usr/local/bin/wait-for-it

RUN curl -sSL https://deb.nodesource.com/gpgkey/nodesource.gpg.key | apt-key add -
RUN curl -fsSL https://deb.nodesource.com/setup_18.x | bash -
RUN apt-get update && \
    apt-get install -y nodejs && \
    apt-get clean && rm -rf /var/lib/apt/lists/* && \
    npm update -g

#######################
######### DEV #########
#######################
FROM base-dev AS dev

RUN apt-get update && apt-get install -y gnupg2 && \
    curl -sSL https://packages.blackfire.io/gpg.key | apt-key add - && \
    echo "deb http://packages.blackfire.io/debian any main" | tee /etc/apt/sources.list.d/blackfire.list && \
    apt-get update && apt-get install -y blackfire-php && \
    printf "blackfire.agent_socket=tcp://blackfire:8707\n" >> $PHP_INI_DIR/conf.d/zz-blackfire.ini && \
    apt-get clean && \
    rm -rf /var/lib/apt/lists/* && \
    apt-get purge -y --auto-remove gnupg2

RUN apt-get update && apt-get install -y \
    $APPIZE_DEPS \
    $PHPIZE_DEPS && \
    mkdir -p /usr/src/php/ext/xdebug && curl -fsSL https://pecl.php.net/get/xdebug | tar xvz -C "/usr/src/php/ext/xdebug" --strip 1 && \
    docker-php-ext-install -j$(nproc) xdebug && \
    rm $PHP_INI_DIR/conf.d/docker-php-ext-xdebug.ini && \
    apt-get purge -y --auto-remove $APPIZE_DEPS $PHPIZE_DEPS && \
    docker-php-source delete

COPY --from=composer/composer:latest-bin /composer /usr/local/bin/composer

COPY docker/supervisord/supervisord.dev.conf /etc/supervisor/conf.d/supervisord.conf
RUN chmod 644 /etc/supervisor/conf.d/supervisord.conf
