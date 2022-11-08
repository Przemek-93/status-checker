1. duplicate env and adapt to your requirements: `cp .env .env.local`
2. create database: `php bin/console doctrine:database:create`
3. execute prepared migrations: `php bin/console doctrine:migrations:migrate`
