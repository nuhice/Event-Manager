FROM php:8.2-apache

RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libpq-dev \
    zip \
    unzip \
    default-mysql-client \
    postgresql-client

RUN apt-get clean && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install pdo_mysql pdo_pgsql mbstring exif pcntl bcmath gd

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY . /var/www/html

RUN composer install --no-dev --optimize-autoloader

COPY apache-config.conf /etc/apache2/sites-available/000-default.conf

RUN a2enmod rewrite headers

RUN mkdir -p /var/www/html/logs && chmod 777 /var/www/html/logs

EXPOSE 80

CMD ["apache2-foreground"]

