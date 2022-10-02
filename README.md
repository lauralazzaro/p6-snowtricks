# P6-SnowTricks

## Technologies

- PHP >= 8.0.2
- MariaDB 10.4.21
- Symfony 6.0

## Installation

- Install Symfony-CLI

> Go to https://symfony.com/download for the instruction based on your operating system

- After the installation of symfony, run the following command to verify if your system is symfony ready:

> symfony check:requirements

- Clone or download the GitHub repository:

> git clone https://github.com/lauralazzaro/snowtricks

- Create a file named '.env.local' place in the root folder of the project and then configure the variables 'MAILER_DSN'
  and 'DATABASE_URL':

> ex:
> MAILER_DSN=smtp://user:pass@smtp.example.com:25
> DATABASE_URL="mysql://db_user:db_password@127.0.0.1:3306/db_name?serverVersion=5.7&charset=utf8mb4"

> - db_user and db_password: *your credentials as database administrator*
> - db_name: **snowtricks**
> - serverVersion: what db engine are you using in your environment

If you get the *sync-metadata-storage* error, verify that you put the right version of the db of your environment in
serverVersion:

> ex:
> .../snowtricks?serverVersion=5.7 or ...snowtricks?serverVersion=MariaDB-10.4.21

For more information on the setup, please visit:

- https://symfony.com/doc/current/mailer.html
- https://symfony.com/doc/current/reference/configuration/doctrine.html

- Install the dependancies with [Composer](https://getcomposer.org/download/)

> composer install

- Create the database using the command:

> php bin/console doctrine:database:create

- Create the tables in the database with:

> php bin/console doctrine:migrations:migrate

- Load the fixtures for the database with:

> php bin/console doctrine:fixtures:load

By default, the load command purges the database, removing all data from every table. To append your fixtures' data add
the --append option.

- Start the server with:

> symfony server:start

- URL for the project:

> http://127.0.0.1:8000

- Users already present in the database that created the initial tricks:

> email: email0@email.com  
> password: pass1234

> email: email1@email.com  
> password: pass1234

> email: email2@email.com  
> password: pass1234

> email: email4@email.com  
> password: pass1234

- Create a new user with your real email to test the reset password