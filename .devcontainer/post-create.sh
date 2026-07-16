#!/usr/bin/env bash

set -euo pipefail

APP_DIR="/workspaces/saber/aluguel-ferramentas"

cd "$APP_DIR"

echo "Instalando dependências do PHP..."
composer install

echo "Instalando dependências do frontend..."
npm install

if [ ! -f ".env" ]; then
    echo "Criando arquivo .env..."
    cp .env.example .env
fi

set_env() {
    local key="$1"
    local value="$2"

    if grep -qE "^${key}=" .env; then
        sed -i "s|^${key}=.*|${key}=${value}|" .env
    else
        printf "\n%s=%s\n" "$key" "$value" >> .env
    fi
}

echo "Configurando conexão com MySQL..."

set_env "DB_CONNECTION" "mysql"
set_env "DB_HOST" "mysql"
set_env "DB_PORT" "3306"
set_env "DB_DATABASE" "aluguel_ferramentas"
set_env "DB_USERNAME" "laravel"
set_env "DB_PASSWORD" "laravel"

if ! grep -qE '^APP_KEY=base64:.+' .env; then
    echo "Gerando chave do Laravel..."
    php artisan key:generate
fi

php artisan config:clear

echo "Aguardando o MySQL ficar disponível..."

until mysql \
    --host=mysql \
    --user=laravel \
    --password=laravel \
    --execute="SELECT 1" \
    aluguel_ferramentas >/dev/null 2>&1
do
    sleep 2
done

echo "Executando migrations no MySQL..."

php artisan migrate --force

echo "Ambiente configurado com sucesso."