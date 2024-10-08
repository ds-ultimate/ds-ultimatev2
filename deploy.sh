#!/bin/sh

# activate maintenance mode
php artisan down --render="errors::503"

# update source code
git pull

# update PHP dependencies
php composer.phar install --no-interaction --no-dev --prefer-dist
	# --no-interaction	Do not ask any interactive question
	# --no-dev		Disables installation of require-dev packages.
	# --prefer-dist		Forces installation from package dist even for dev versions.


# clear cache
php artisan cache:clear

# clear config cache
php artisan config:clear

# cache config
php artisan config:cache

# clear cached views
php artisan view:clear

# update database
php artisan migrate --force
	# --force		Required to run when in production.

# stop maintenance mode
php artisan up
