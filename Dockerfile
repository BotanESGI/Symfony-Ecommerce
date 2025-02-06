# Stage 1: Build
FROM chialab/php-dev:8.3-fpm-alpine AS builder

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Install dependencies
WORKDIR /app
COPY . .
RUN composer install --prefer-dist

# Stage 2: Production
FROM chialab/php-dev:8.3-fpm-alpine

# Install Symfony CLI
RUN apk update && apk add bash wget && \
    wget https://get.symfony.com/cli/installer -O - | bash && \
    mv /root/.symfony*/bin/symfony /usr/local/bin/symfony

# Set the working directory
WORKDIR /app

# Copy the app from the builder stage
COPY --from=builder /app .

# Install production dependencies
RUN composer install --no-dev --optimize-autoloader

# Copy the initialization script
COPY init_db_test.sh /usr/local/bin/init_db_test.sh
RUN chmod +x /usr/local/bin/init_db_test.sh

# Expose the port
EXPOSE 8000

# Command to start the Symfony server
CMD ["symfony", "server:start", "--port=8000", "--dir=./public", "--listen-ip=0.0.0.0"]
