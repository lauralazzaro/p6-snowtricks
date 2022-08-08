# P6-SnowTricks
## Technologies
- PHP >= 8.0.2
- MariaDB 10.4.21
- Symfony 6.0

## Installation
1. Install Symfony-CLI
```
    Go to https://symfony.com/download for the instruction based on your operating system
```
2. After the installation of symfony, run the following command to verify if your system is symfony ready:
```
    symfony check:requirements
```
3. Clone or download the GitHub repository:
```
    git clone https://github.com/lauralazzaro/snowtricks
```

2. Setup the variable 'MAILER_DSN' and 'DATABASE_URL' in the '.env.local' file that you will create and place in the root folder of the project
```
    ex: 
    MAILER_DSN=smtp://user:pass@smtp.example.com:25
    DATABASE_URL="mysql://db_user:db_password@127.0.0.1:3306/db_name?serverVersion=5.7&charset=utf8mb4"
```
For more information on the setup, please visit:
- https://symfony.com/doc/current/mailer.html
- https://symfony.com/doc/current/reference/configuration/doctrine.html
3. Install the dependancies with [Composer](https://getcomposer.org/download/)
```
    composer install
```
4. Create the database using the command:
```
    php bin/console doctrine:database:create
```
5. Create the tables in the database with:
```
    php bin/console doctrine:migrations:migrate
```
6. Start the server with:
```
    symfony server:start
```
7. URL for the project:
```
    http://127.0.0.1:8000
```
