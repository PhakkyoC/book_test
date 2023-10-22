Pour ce projet j'ai choisi d'utiliser une base SQLite3 qui ne demande pas d'installation particuliaire.

version de php utiliser : php8.1.2

Instalation : 

composer install 

bin/console doctrine:database:create

bin/console doctrine:migrations:migrate

bin/console doctrine:fixtures:load -n

symfony server:start

https://127.0.0.1:8000/