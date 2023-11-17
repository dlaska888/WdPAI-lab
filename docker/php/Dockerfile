FROM php:8.2.9-fpm

# Debug
RUN pecl install xdebug
RUN docker-php-ext-enable xdebug

# Copy the application code
COPY . /app

VOLUME ["/app"]
