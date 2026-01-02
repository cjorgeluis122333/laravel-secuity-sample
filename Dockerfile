# Etapa 1: Instalar dependencias con Composer (requiere internet solo si no preinstalado)
FROM composer:2 AS composer
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --prefer-dist

# Etapa 2: Entorno de ejecución con PHP
FROM php:8.2-cli

# Instalar extensiones necesarias para Laravel
RUN apt-get update && apt-get install -y \
    git \
    zip \
    unzip \
    sqlite3 \
    && docker-php-ext-install pdo pdo_sqlite mbstring exif pcntl bcmath sockets \
    && rm -rf /var/lib/apt/lists/*

# Establecer directorio de trabajo
WORKDIR /var/www/html

# Copiar dependencias y código
COPY --from=composer /app/vendor ./vendor
COPY . .

# Asegurar permisos para SQLite y logs
RUN chown -R www-data:www-data /var/www/html/storage \
    && chown -R www-data:www-data /var/www/html/database \
    && chmod -R 775 /var/www/html/storage \
    && chmod -R 775 /var/www/html/database

# Exponer puerto (si usas php artisan serve)
EXPOSE 8000

# Cambiar a usuario no root (mejor práctica)
USER www-data

# Iniciar la aplicación
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
