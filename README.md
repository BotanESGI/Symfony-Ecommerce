Installation & lancement :

1. Lancer `docker compose build --no-cache` pour construire le docker
2. Lancer `docker compose up --pull always -d --wait` pour lancer le docker
3. Lancer `composer install` pour installer composer
4. Lancer `docker-compose exec php php bin/console doctrine:migrations:migrate` pour creer les tables dans la bdd
5. Lancer `docker-compose exec php php bin/console doctrine:fixtures:load` pour lancer les fixtures
6. Lancer `http://localhost:8000/`

### Documentation :

- Un cahier des charges ✅
- Un schéma de la BDD (En cours)
- Des fixtures ✅
- Un guide (readme) pour installer le projet en local, le démarrer, les comptes de tests, process de validation ... (Pas finis) ✅

### Entités :
- Au minimum 10 entités avec de l'héritage d'entité ✅
- Au minimum 2 relations ManyToMany ✅
- Au minimum 8 relations OneToMany ✅

### Sécurité :
- Une authentification sécurisée ✅
- Au moins 1 voter personnalisé
- Avoir 3 rôles différents pour les permissions ✅

### API :
- Avoir au moins un controller dédie pour une API (JSON, normalizer, denormalizer)
- Avoir un envoi de mail (local pour l'instant) ✅
- Accéder à une API externe (IA ? SMS ? Autre ?) (Stripe) ✅

### Autre :
- Avoir un minimum 1 test unitaire et 1 test fonctionnel
- Avoir des requêtes personnalisées avec des query builder dans des repositories ✅
- Utiliser des forms dynamiques ✅
- Avoir un espace admin (En cous)
- Minimum 10 pages différentes (A compter) ✅

### CI/CD :
- Projet déployé
- Une CI qui fait tourner les tests, l'analyse statique PHPStan, un linter ... ?

## Points bonus :

- Temps réel
- Asynchrone
- Commandes personnalisées
- Tests mutations
- DDD
- TDD
