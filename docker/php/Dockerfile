FROM php:8.2.9-fpm

WORKDIR /app

ENV RUN_DEPS \
    zlib1g \
    libzip4 \
    libpng16-16 \
    libjpeg62-turbo \
    libpq5

ENV BUILD_DEPS \
    zlib1g-dev \
    libzip-dev \
    libpng-dev \
    libjpeg62-turbo-dev \
    libpq-dev

ENV PHP_EXTENSIONS \
    opcache \
    zip \
    gd \
    bcmath \
    pgsql \
    pdo_pgsql

# Install required packages and development tools
RUN apt-get update && apt-get install -y --no-install-recommends $BUILD_DEPS \
    && docker-php-ext-configure gd --with-jpeg \
    && docker-php-ext-install -j "$(nproc)" $PHP_EXTENSIONS \
    && apt-get purge -y --auto-remove -o APT::AutoRemove::RecommendsImportant=false $BUILD_DEPS \
    && rm -rf /var/lib/apt/lists/*

# Install runtime dependencies
RUN apt-get update && apt-get install -y --no-install-recommends $RUN_DEPS \
    && rm -rf /var/lib/apt/lists/*

# Debug
RUN pecl install xdebug
RUN docker-php-ext-enable xdebug

# Copy the application code
COPY . /app

COPY --from=composer:2.7.2 /usr/bin/composer /usr/local/bin/composer
RUN alias composer="php /usr/local/bin/composer/composer.phar"

RUN composer require php-webdriver/webdriver
RUN composer install
