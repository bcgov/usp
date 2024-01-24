#!/bin/bash

if [ $(id -u) -ne 0 ]; then
    echo "Script should be run as root during buildtime."
    exit 1
else
    echo "Running as root that's cool :)"
fi

# System - set exec on scripts in /docker-bin/
echo "Set exec mode on '/docker-bin/*.sh'"
chmod a+rx /docker-bin/*.sh 

# System - set the proper timezone
if [ -n "${TZ}" ]; then
	ln -snf "/usr/share/zoneinfo/$TZ" "/etc/localtime"
	echo "$TZ" > /etc/timezone
fi

# System - Add extra ca-certificate to system certificates
if [ -n "${CA_HOSTS_LIST}" ]; then
    for hostAndPort in ${CA_HOSTS_LIST}; do
        echo "Adding ca-certificate of ${hostAndPort}"
        openssl s_client -connect ${hostAndPort} -showcerts < /dev/null | awk '/BEGIN/,/END/{ if(/BEGIN/){a++}; out="/usr/local/share/ca-certificates/'${hostAndPort}'"a".crt"; print >out}'
    done
    update-ca-certificates
fi

tz=$(ls -l "/etc/localtime" | awk '{print $NF}' | sed -e 's#/usr/share/zoneinfo/##g')
echo "TZ: ${TZ:-default} (effective ${tz})"

# Apache - fix cache directory
if [ -d /var/cache/apache2 ]; then
    chgrp -R 0 /var/cache/apache2
    chmod -R g=u /var/cache/apache2
fi