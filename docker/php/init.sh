#!/bin/bash

composer --no-interaction install

# Create database
bin/console doctrine:database:drop -f --if-exists
bin/console doctrine:database:create --if-not-exists
bin/console doctrine:migrations:migrate --no-interaction
bin/console doctrine:fixtures:load --no-interaction

# Create test database
bin/console doctrine:database:drop --env=test -f --if-exists
bin/console doctrine:database:create --env=test --if-not-exists
bin/console doctrine:migrations:migrate --env=test --no-interaction
bin/console doctrine:fixtures:load --env=test --no-interaction

exec "$@"
