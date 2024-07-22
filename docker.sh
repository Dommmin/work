#!/usr/bin/env bash

set -e

USER=dominik
USER_ID=1000

BOLD="$(tput bold)"
RED="$(tput setaf 1)"
GREEN="$(tput setaf 2)"
YELLOW="$(tput setaf 3)"
RESET="$(tput sgr0)"

BASEDIR=$(dirname "$0")
ENV_FILE="$BASEDIR/.env"

if [ ! -f "$ENV_FILE" ]; then
    echo "${BOLD}${RED}$ENV_FILE does not exist.${RESET}"
    exit 1
fi

PROJECT_ENV_FILE="$BASEDIR/.env"

eval "$(grep ^DOCKER_PREFIX= $ENV_FILE)"
eval "$(grep ^DBNAME= $ENV_FILE)"
eval "$(grep ^DOCKER_IP= $ENV_FILE)"
eval "$(grep ^DOCKER_PORT= $ENV_FILE)"

DOCKER_PREFIX=$(echo $DOCKER_PREFIX | tr -d '[:space:]')

echo "${YELLOW}Uruchomienie kontenerów dla projektu ${BOLD}${DOCKER_PREFIX}${RESET}"
docker-compose up -d

# Dodanie użytkownika, jeśli nie istnieje
if ! docker exec -it -u root "$DOCKER_PREFIX"_php id -u $USER > /dev/null 2>&1; then
    echo -e "${BOLD}${YELLOW}Dodanie użytkownika ${USER}${RESET}\n"
    docker exec -it -u root "$DOCKER_PREFIX"_php addgroup -g $USER_ID $USER
    docker exec -it -u root "$DOCKER_PREFIX"_php adduser -u $USER_ID -G $USER -h /home/$USER -D $USER
else
    echo "${BOLD}${RED}Użytkownik ${USER} już istnieje${RESET}"
fi

# Ustawienie uprawnień dla katalogu
echo -e "${BOLD}${YELLOW}Ustawienie uprawnień dla katalogu${RESET}\n"
docker exec -it -u root "$DOCKER_PREFIX"_php chown -R $USER:$USER /var/www/html

# Instalacja Composera
echo "${BOLD}${RED}--------------------------------------------------------------------------------${RESET}"
echo -e "${BOLD}${YELLOW}Instalacja Composera${RESET}\n"
docker exec -it -u $USER "${DOCKER_PREFIX}_php" composer install --no-scripts --no-interaction

# Uruchomienie PHP CS Fixer
echo -e "${BOLD}${YELLOW}Uruchomienie PHP CS Fixer${RESET}\n"
docker exec -it -u $USER "${DOCKER_PREFIX}_php" ./vendor/bin/php-cs-fixer fix

# Uruchomienie PHPStan (nie przerywa skryptu w przypadku błędów)
echo -e "${BOLD}${YELLOW}Uruchomienie PHPStan${RESET}\n"
docker exec -it -u $USER "${DOCKER_PREFIX}_php" ./vendor/bin/phpstan analyse src tests --memory-limit=1G || true

# Uruchomienie PHP Insights
echo -e "${BOLD}${YELLOW}Uruchomienie PHP Insights${RESET}\n"
docker exec -it -u $USER "${DOCKER_PREFIX}_php" php vendor/bin/phpinsights fix

# TODO create test database
## Uruchomienie PHPUnit
#echo -e "${BOLD}${YELLOW}Uruchomienie PHPUnit${RESET}\n"
#docker exec -it -u $USER "${DOCKER_PREFIX}_php" ./vendor/bin/phpunit

# Uruchomienie migracji
echo -e "${BOLD}${YELLOW}Uruchamianie migracji${RESET}\n"
docker exec -it -u $USER "${DOCKER_PREFIX}_php" php bin/console doctrine:migrations:migrate --no-interaction

echo "${YELLOW}Aplikacja jest dostępna pod adresem: ${BOLD}${GREEN}http://localhost:80${RESET}"
