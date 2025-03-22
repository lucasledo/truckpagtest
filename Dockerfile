# Imagem base
FROM php:8.3-fpm

# Definir diretório de trabalho
WORKDIR /var/www/html

# Instalar dependências do sistema
RUN apt-get update && apt-get install -y \
    unzip \
    git \
    curl \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libonig-dev \
    libxml2-dev \
    libcurl4-openssl-dev \
    pkg-config \
    libssl-dev \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Instalar extensão MongoDB
RUN pecl install mongodb \
    && docker-php-ext-enable mongodb

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copiar arquivos do Laravel para dentro do container
COPY . /var/www/html

# Instalar dependências do Laravel
RUN composer install --no-dev --optimize-autoloader

# Garantir que os diretórios existem antes de alterar permissões
RUN mkdir -p /var/www/html/storage /var/www/html/storage/framework/{cache,sessions,views} /var/www/html/bootstrap/cache \
    && chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

RUN php artisan migrate --force

# Expor porta
EXPOSE 9000

CMD ["php-fpm"]
