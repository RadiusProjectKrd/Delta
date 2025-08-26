#!/bin/bash
set -e

# Здесь можно добавить команды инициализации, если нужно что-то кастомное.
# Обычно PostgreSQL уже создает базу postgres, но если нужно, можно управлять.

echo "Initialization script running..."

# Например, можно создать дополнительные базы или пользователей,
# но системная база postgres создается по умолчанию.

# Если нужно, можно выполнить любые sql-скрипты через psql:
# psql -v ON_ERROR_STOP=1 --username "$POSTGRES_USER" <<-EOSQL
#     CREATE DATABASE mydb;
# EOSQL

