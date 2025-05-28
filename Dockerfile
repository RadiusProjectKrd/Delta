FROM php:8.3-fpm

WORKDIR /var/www

COPY src/composer.lock src/composer.json /var/www/
RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    nano \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    locales \
    zip \
    jpegoptim optipng pngquant gifsicle \
    vim \
    unzip \
    git \
    curl \
    libpq-dev \
    libonig-dev \
    libzip-dev && \
    docker-php-ext-configure gd --with-freetype --with-jpeg && \
    docker-php-ext-install pdo_pgsql mbstring zip exif pcntl gd && \
    apt-get clean && rm -rf /var/lib/apt/lists/*

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN groupadd -g 1000 www
RUN useradd -u 1000 -ms /bin/bash -g www www
COPY src /var/www
COPY --chown=www:www src /var/www
USER www
EXPOSE 9000
CMD ["php-fpm"]