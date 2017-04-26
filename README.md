Employees dashboard
========================

## Installation
### Install project via Composer
composer install

### Migrate database
bin/console doctrine:migrations:migrate

It migrates to initial state of DB.

### Load fixtures
bin/console doctrine:fixtures:load

It loads 300 dummy employees and 4 dummy departments into DB.

Index page shows all of them with pagination and filter. Also one can do to page /alphabetic to see employees divided into alphabetical groups.
