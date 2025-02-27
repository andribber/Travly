FROM php:8.4-fpm-alpine3.19

# Set the working directory
WORKDIR /var/www

# Copy the current directory contents into the container
COPY . /var/www

# Install system dependencies
RUN apk add --no-cache \
    bash \
    curl \
    libpng-dev \
    libzip-dev \
    zlib-dev

# Install MySQL PDO
RUN docker-php-ext-install pdo_mysql

# Rename it to php.ini
RUN mv /usr/local/etc/php/php.ini-development /usr/local/etc/php/php.ini \
    && sed -i "/listen = .*/c\listen = [::]:9000" /usr/local/etc/php-fpm.d/www.conf

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY ./scripts/wait-for-it.sh /usr/local/bin/wait-for-it.sh
RUN chmod +x /usr/local/bin/wait-for-it.sh

COPY ./scripts/postrun.sh /usr/local/bin/postrun.sh
RUN chmod +x /usr/local/bin/postrun.sh
ENTRYPOINT ["/usr/local/bin/postrun.sh"]

EXPOSE 9000

CMD ["php-fpm"]
