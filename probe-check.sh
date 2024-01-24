#!/bin/sh

### Check if vendor directory does not exist ###
### composer did not install ###
if [ ! -d "/var/www/html/vendor" ]; then
    exit 1
fi
exit 0
