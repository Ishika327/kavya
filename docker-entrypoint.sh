
#!/bin/sh

set -e

cd /var/www/html

mkdir -p storage/framework/cache
mkdir -p storage/framework/sessions
mkdir -p storage/framework/views
mkdir -p bootstrap/cache

# Create SQLite database if it does not exist
touch database/database.sqlite

chown -R www-data:www-data storage bootstrap/cache database

# Prepare Laravel configuration
php artisan config:cache

# Run database migrations
php artisan migrate --force

# Start Apache
exec apache2-foreground
