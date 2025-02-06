#!/bin/sh

# Attendre que la bdd est prête
until nc -z database 5432; do
  echo "Waiting for the database..."
  sleep 2
done

# Créer la base de données pour l'environnement de test et lance les migrations
php bin/console doctrine:database:create --env=test
php bin/console doctrine:migrations:migrate --env=test

# Créer la base de données pour l'environnement de test et lance les migrations
php bin/console doctrine:database:create --env=dev
php bin/console doctrine:migrations:migrate --env=dev
