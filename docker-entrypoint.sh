
#!/bin/sh
set -e

cd /var/www/html

if [ ! -f database/database.sqlite ]; then
    touch database/database.sqlite
fi

chown -R www-data:www-data storage bootstrap/cache database

php artisan config:clear

if [ -f artisan ]; then
    php artisan migrate --force
fi

exec apache2-foreground
