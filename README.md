# e-commerce_ynov

1 - git clone https://github.com/lionel-kg/e-commerce_ynov.git

pré requis :

minimum PHP 7.3

2 - composer install

3 - php bin/console doctrine:database:create

4 - php bin/console doctrine:schema:create

5 - php .\bin\console doctrine:fixtures:load --append

Demarrer le server 

1 - php -S 127.0.0.1:8000 -t public

compte administrateur :

 email : admin@gmail.com
 mot de passe : password

