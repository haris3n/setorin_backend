FROM php:8.2-fpm-alpine

# Install dependencies sistem (Lengkap untuk Alpine)
RUN apk add --no-cache \
    nginx \
    nodejs \
    npm \
    curl \
    zip \
    unzip \
    git \
    supervisor \
    libpng-dev \
    libzip-dev \
    oniguruma-dev \
    libxml2-dev \
    icu-dev \
    freetype-dev \
    libjpeg-turbo-dev

# Install ekstensi PHP 
# Khusus GD di Alpine perlu konfigurasi library jpeg dan freetype
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install \
    pdo_mysql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd \
    opcache \
    zip \
    intl

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copy file project
COPY . .

# Tambahkan baris ini sebelum composer install untuk menghindari masalah permission saat build
RUN mkdir -p storage/framework/sessions storage/framework/views storage/framework/cache

# Install dependencies Laravel (Gunakan --no-scripts untuk menghindari error jika belum ada env)
RUN composer install --optimize-autoloader --no-dev --no-scripts

# Set permission
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Copy konfigurasi Nginx dan Supervisor
COPY docker/nginx.conf /etc/nginx/nginx.conf
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

EXPOSE 80

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]

# Install Node & build frontend
COPY package*.json ./
RUN npm ci
RUN npm run build