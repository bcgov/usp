#!/bin/bash

echo "Start entrypoint file"

echo "APACHE_REMOTE_IP_HEADER: ${APACHE_REMOTE_IP_HEADER}"
echo "APACHE_REMOTE_IP_TRUSTED_PROXY: ${APACHE_REMOTE_IP_TRUSTED_PROXY}"
echo "APACHE_REMOTE_IP_INTERNAL_PROXY: ${APACHE_REMOTE_IP_INTERNAL_PROXY}"

echo "Setup TZ"
php -r "date_default_timezone_set('${TZ}');"
php -r "echo date_default_timezone_get();"

if [ -f /vault/secrets/secrets.env ]; then
    touch .env && cp -rf /vault/secrets/secrets.env /var/www/html/.env
fi
if [ -f /vault/secrets/test-secrets.env ]; then
    touch .env && cp -rf /vault/secrets/test-secrets.env /var/www/html/.env
fi
echo "ENV_ARG: ${ENV_ARG}"

echo "Install composer"
composer dump-auto

chmod 766 /var/www/html/probe-check.sh

echo "Permissions setup for NPM:"
chmod -R a+w node_modules

echo "Starting apache:"
/usr/sbin/apache2ctl start

echo "Restarting apache:"
/usr/sbin/apache2ctl restart


echo "End entrypoint"
while :; do
sleep 300
done
