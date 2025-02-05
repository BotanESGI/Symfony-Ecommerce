FROM chialab/php-dev:8.3-fpm-alpine

# Installer Symfony CLI et Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

RUN apk update && apk add bash wget

# Installer Symfony CLI
RUN wget https://get.symfony.com/cli/installer -O - | bash && \
    mv /root/.symfony*/bin/symfony /usr/local/bin/symfony

# Définir le répertoire de travail
WORKDIR /app

# Copier le script d'initialisation
COPY init_db_test.sh /usr/local/bin/init_db_test.sh
RUN chmod +x /usr/local/bin/init_db_test.sh

# Exposer le port
EXPOSE 8000

# Commande pour démarrer le serveur Symfony
CMD ["symfony", "server:start", "--port=8000", "--dir=./public", "--listen-ip=0.0.0.0"]
