#!/bin/bash
case $1 in
  development)
    rsync -r --delete-after --exclude-from 'scripts/exclude.txt' --quiet $TRAVIS_BUILD_DIR/ git@jyps.fi:/usr/share/nginx/betarekkari.jyps.fi
    ssh git@jyps.fi "php /usr/share/nginx/betarekkari.jyps.fi/app/console cache:clear --env=dev"
    ;;
  production)
    rsync -r --delete-after --exclude-from 'scripts/exclude.txt' --quiet $TRAVIS_BUILD_DIR/ git@jyps.fi:/usr/share/nginx/jasenrekisteri.jyps.fi
    ssh git@jyps.fi "php /usr/share/nginx/jasenrekisteri.jyps.fi/app/console cache:clear --env=prod"
    ;;
esac
