#Symfony 4


## Clone
- `composer install`
- `php bin/console doctrine:database:create`
- `php bin/console make:migration`
- `php bin/console doctrine:migrations:migrate`

## Production
- `composer install --no-dev --optimize-autoloader`