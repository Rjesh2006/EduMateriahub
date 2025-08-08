# Step 1: Base image
FROM php:8.2-cli

# Step 2: Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    zip \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    && docker-php-ext-install pdo pdo_mysql mbstring zip exif pcntl

# Step 3: Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Step 4: Set working directory
WORKDIR /var/www

# Step 5: Copy project files
COPY . .

# Step 6: Install Laravel dependencies
RUN composer install

# Step 7: Copy environment file
RUN cp .env.example .env

# Step 8: Generate app key
RUN php artisan key:generate

# Step 9: Ensure the SQLite file exists
RUN touch /var/www/database/database.sqlite

# Step 10: Run database migrations
RUN php artisan migrate --force

# Step 11: Expose Laravel development port
EXPOSE 8000

# Step 12: Start Laravel development server
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
