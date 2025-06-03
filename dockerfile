# Usa imagen base oficial con PHP + FPM
FROM php:8.2-fpm

# Instalar dependencias necesarias y limpiar caché
RUN apt-get update && apt-get install -y \
    git unzip curl libpq-dev libzip-dev libonig-dev \
    libxml2-dev zip sqlite3 \
    libpng-dev libjpeg-dev libfreetype6-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_pgsql pdo_mysql zip gd \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Instalar Composer desde la imagen oficial
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Directorio de trabajo
WORKDIR /var/www/html

# Copiar solo composer.json y composer.lock para aprovechar cache de dependencias
COPY composer.json composer.lock ./

# Copiar el resto del código fuente (incluyendo artisan)
COPY . .

# Instalar dependencias de Laravel (sin dev por defecto, configurable por ARG)
ARG COMPOSER_FLAGS="--optimize-autoloader --no-dev"
RUN composer install $COMPOSER_FLAGS || composer install --ignore-platform-reqs $COMPOSER_FLAGS

# Instalar Node.js y npm
RUN curl -fsSL https://deb.nodesource.com/setup_18.x | bash - \
    && apt-get install -y nodejs

# Instalar dependencias de npm y compilar assets con Vite
RUN npm install && npm run build

# Crear carpetas necesarias y asignar permisos
RUN mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views storage/logs bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache

# Exponer el puerto de la app
EXPOSE 8000

# Comando por defecto: migrar, seedear y servir
CMD php artisan config:clear && php artisan migrate --force && php artisan db:seed --force && php artisan serve --host=0.0.0.0 --port=8000
