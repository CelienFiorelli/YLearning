
# YLearning - API

## Installation

- Cloner le répo avec `git clone https://github.com/CelienFiorelli/YLearning.git`
- Installer toutes les dépendances avec `composer install`

- Dupliquer le fichier .env et le nommer .env.local puis compléter les informations de base de données mysql

- Créer la base de donnée avec `php bin/console doctrine:database:create`
- Puis appliquer les migrations `php bin/console d:s:u --force`

- Utiliser les seeders pour la base de donnée `php bin/console doctrine:fixtures:load`

- Créer le dossier `jwt` dans `/config` puis faire les commandes suivante :
```bash
openssl genpkey -out config/jwt/private.pem -aes256 -algorithm rsa -pkeyopt rsa_keygen_bits:4096
openssl pkey -in config/jwt/private.pem -out config/jwt/public.pem -pubout
```

- Lancer le projet avec `symfony serve` à la racine du projet


## A savoir

- Le mot de passe de chaque utilisateur est "user"

## Le projet

Le but de l'api est de gérer une plateforme permettant à la fois de consulter des cours et de pouvoir participer à des challenges
sous forme de problématique a résoudre dans le langage choisis