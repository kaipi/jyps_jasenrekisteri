#!/bin/bash
case $1 in
  development)
    rsync -r --delete-after --quiet $TRAVIS_BUILD_DIR/ git@jyps.fi:/usr/share/nginx/betarekkari.jyps.fi
    ssh git@jyps.fi "php /usr/share/nginx/betarekkari.jyps.fi/app/console cache:clear --env=dev"
    ;;
  production)
    rsync -r --delete-after --quiet $TRAVIS_BUILD_DIR/ git@jyps.fi:/us/deplotesti
    #ssh git@jyps.fi "php /usr/share/nginx/betarekkari.jyps.fi/app/console cache:clear --env=production"
    ;;
esac
