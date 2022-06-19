#!/bin/sh

# activate maintenance mode
/opt/keyhelp/php/8.0/bin/php artisan down --render="errors::503"

# update source code
git pull

# update PHP dependencies
/opt/keyhelp/php/8.0/bin/php composer.phar install --no-interaction --no-dev --prefer-dist
	# --no-interaction	Do not ask any interactive question
	# --no-dev		Disables installation of require-dev packages.
	# --prefer-dist		Forces installation from package dist even for dev versions.


# clear cache
/opt/keyhelp/php/8.0/bin/php artisan cache:clear

# clear config cache
/opt/keyhelp/php/8.0/bin/php artisan config:clear

# cache config
/opt/keyhelp/php/8.0/bin/php artisan config:cache

# clear cached views
/opt/keyhelp/php/8.0/bin/php artisan view:clear

# update database
/opt/keyhelp/php/8.0/bin/php artisan migrate --force
	# --force		Required to run when in production.

# stop maintenance mode
/opt/keyhelp/php/8.0/bin/php artisan up
