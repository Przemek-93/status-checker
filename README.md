# Syngeos Status-Checker application

Technologies: `PHP 8.1`, `Symfony 6.1`

Syngeos application monitoring system, its function is to periodically check the system (based on added by user checking-requests)
and inform users if something is not working as it should.
The system has a dashboard for adding/editing requests for registered users and a status page for everyone.
The system fetch the readings and stores them in a database, it has a number of commands that should be run at hourly intervals.


### Preparing environment

* Execute `cp .env.dist .env` to get local copy of .env file and adjust the environment.

* Execute `docker-compose build` and then `docker-compose up` to build and run docker environment.

* Execute `bin/console doctrine:database:create` to create database (only for non-docker).

* Execute `docker-compose exec php bin/console doctrine:migrations:migrate` to
  apply all changes to database (this command should be executed each time you pull some changes from branches to make
  sure that you have current version of database scheme).


### Application commands

The text file with the cron configuration can be found [here](./cron.txt) 

* te execute checking statuses
  run `docker-compose exec php bin/console status-checker:check` - command trigger a checking of all active checking-requests
    - this command can be run less frequently, few times (5-6) a day


* to remove outdated readings
  run `docker-compose exec php bin/console status-checker:remove-readings --readings-count 30` - command will keep the number of `30` most recent readings, the rest will be deleted
    - `--readings-count` - count of reading that will be retained in the database, defaults to `30` so you can omit this argument
    - this command should be executed every hour after status-checker:execute


* main process, to run a check and send an email alert when something goes wrong
  run `docker-compose exec php bin/console status-checker:execute` - command will first run a checking and check all active requests, 
  then will save the readings to the database and finally will send a notification to the saved notification-email addresses if something is wrong
    - this command should be executed twice per hour
