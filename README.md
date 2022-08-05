# P6-SnowTricks

## Installation
1. Clone or download the GitHub repository:
```
    git clone https://github.com/lauralazzaro/snowtricks
```

2. Setup the variable 'MAILER_DSN' and 'DATABASE_URL' in the '.env' file located in the root folder of the project


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
