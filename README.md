# Syngeos Status-Checker application

Technologies: 
[PHP 8.1](https://www.php.net/releases/8.1/en.php),
[Symfony 6.1](https://symfony.com/doc/6.1/index.html)

Syngeos application monitoring system, its function is to periodically check the system (based on added by user checking-requests)
and inform users if something is not working as it should.\
The system has a dashboard for adding/editing requests for registered users and a status page for everyone.\
The system fetch the readings and stores them in a database, it has a number of commands that should be run at hourly intervals.


### Preparing environment

* Execute `cp .env.dist .env` to get local copy of .env file and adjust the environment.


* Execute `docker-compose build` and then `docker-compose up` to build and run docker environment.


* Execute `bin/console doctrine:database:create` to create database (only for non-docker).


* Execute `docker-compose exec php bin/console doctrine:migrations:migrate` to
  apply all changes to database (this command should be executed each time you pull some changes from branches to make
  sure that you have current version of database scheme).


* Execute `docker-compose exec php bin/console hautelook:fixtures:load` to load fixtures into database.\
  Default user: email `status-checker@syngeos.pl`, password `SyngeosAdmin#`.\
  Fixtures contains also defined default notifications, all data are available [here](./fixtures)


* Make sure if `user registration` is enabled otherwise, it will not be possible to register a new user.\
  Registration can be triggered by [env](./.env) variable `USER_REGISTRATION_ENABLED` which is enabled by default.


### Application commands

The text file with the cron configuration can be found [here](./cron.txt) 

* main process, to run a check and send an email alert when something goes wrong
  run `docker-compose exec php bin/console status-checker:execute` - command will first run a checking and check all active requests,
  then will save the readings to the database and finally will send a notification to the saved notification-email addresses if something is wrong
  - this command should be executed twice per hour


* all emails sent from the platform are handled by symfony messenger (saved in messenger table queue)
  run `docker-compose exec php bin/console messenger:consume email_sender --time-limit 60 --limit 10` - command will automatically exit once it has processed `10` messages (limit),
  or been running for `60s` (time limit)
  - `--time-limit` - running-time of command, passed in seconds
  - `--limit` - number of processed messages after which the command stops
  - this command should be executed 5 times per hour


* te execute checking statuses
  run `docker-compose exec php bin/console status-checker:check` - command trigger a checking of all active checking-requests
    - this command can be run less frequently, few times (5-6) a day


* to remove outdated readings
  run `docker-compose exec php bin/console status-checker:remove-readings --readings-count 30` - command will keep the number of `30` most recent readings, the rest will be deleted
    - `--readings-count` - count of reading that will be retained in the database, default to `30` so you can omit this argument
    - this command should be executed every hour after status-checker:execute


### Testing application

* [PHP_CodeSniffer](https://github.com/squizlabs/PHP_CodeSniffer)\
  execute: `docker-compose exec php vendor/bin/phpcs -p` to show violations\
  execute: `docker-compose exec php vendor/bin/phpcbf -p` to automatically fix violations\
  config: [`phpcs.xml`](./phpcs.xml.dist)


* [Psalm](https://psalm.dev/) \
  execute: `docker-compose exec php vendor/bin/psalm`\
  config: [`psalm.xml`](./psalm.xml)


* [PHPMD](https://phpmd.org/)\
  execute: `docker-compose exec php vendor/bin/phpmd src,public,tests text phpmd.xml`\
  config: [`phpmd.xml`](./phpmd.xml)


* [PHPUnit](https://phpunit.de/)\
  execute: `docker-compose exec php vendor/bin/phpunit`\
  config: [`phpunit.xml`](./phpunit.xml.dist)
