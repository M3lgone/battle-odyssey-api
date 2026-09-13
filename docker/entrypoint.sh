#!/usr/bin/env sh
# Minimal wait-for-db entrypoint.
# Waits until MySQL (service `db`) accepts connections, then execs CMD
# (default: `php artisan serve`). Never runs migrations or seeders.
set -e

HOST="${DB_HOST:-db}"
PORT="${DB_PORT:-3306}"
USER="${DB_USERNAME:-battle}"
export WAIT_HOST="$HOST" WAIT_PORT="$PORT" WAIT_USER="$USER" WAIT_PASS="${DB_PASSWORD:-battle}"

ATTEMPTS="${DB_WAIT_ATTEMPTS:-30}"

i=1
while [ "$i" -le "$ATTEMPTS" ]; do
    if php -r 'try { new PDO("mysql:host=".getenv("WAIT_HOST").";port=".getenv("WAIT_PORT"), getenv("WAIT_USER"), getenv("WAIT_PASS")); exit(0); } catch (Throwable $e) { exit(1); }' 2>/dev/null; then
        break
    fi
    echo "Waiting for MySQL at $HOST:$PORT... ($i/$ATTEMPTS)"
    sleep 2
    i=$((i + 1))
done

if [ "$i" -gt "$ATTEMPTS" ]; then
    echo "MySQL at $HOST:$PORT did not become ready in time." >&2
    exit 1
fi

exec "$@"
