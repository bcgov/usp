#!/bin/bash

echo "Start entrypoint file"

echo "APACHE_REMOTE_IP_HEADER: ${APACHE_REMOTE_IP_HEADER}"
echo "APACHE_REMOTE_IP_TRUSTED_PROXY: ${APACHE_REMOTE_IP_TRUSTED_PROXY}"
echo "APACHE_REMOTE_IP_INTERNAL_PROXY: ${APACHE_REMOTE_IP_INTERNAL_PROXY}"

echo "Setup TZ"
php -r "date_default_timezone_set('${TZ}');"
php -r "echo date_default_timezone_get();"

touch .env && cp -rf /vault/secrets/secrets.env /var/www/html/.env
echo "ENV_ARG: ${ENV_ARG}"

echo "Install composer"
rm -rf vendor
rm -f composer.lock
composer install

echo "Update artisan"
php artisan key:generate --force

chmod 766 /var/www/html/probe-check.sh

echo "Run NPM:"
npm install --prefix /var/www/html/
chmod -R a+w node_modules
npm run --prefix /var/www/html/ prod

echo "Starting apache:"
/usr/sbin/apache2ctl start

echo "Restarting apache:"
/usr/sbin/apache2ctl restart


echo "End entrypoint"
while :; do
sleep 300
done
