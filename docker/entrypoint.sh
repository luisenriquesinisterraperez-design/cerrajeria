#!/usr/bin/env bash
set -euo pipefail

cd /var/www/html

# Asegura permisos sobre directorios escribibles (útil cuando logs/tmp son volúmenes)
mkdir -p logs tmp/cache/models tmp/cache/persistent tmp/cache/views tmp/sessions
chown -R www-data:www-data logs tmp || true
chmod -R 775 logs tmp || true

# Migraciones (sólo si se proporcionó DATABASE_URL)
if [ -n "${DATABASE_URL:-}" ]; then
    if [ "${RUN_MIGRATIONS:-true}" = "true" ]; then
        echo "[entrypoint] Ejecutando migraciones..."
        php bin/cake.php migrations migrate --no-lock || {
            echo "[entrypoint] Las migraciones fallaron." >&2
            exit 1
        }
    else
        echo "[entrypoint] RUN_MIGRATIONS=false, omitiendo migraciones."
    fi
else
    echo "[entrypoint] DATABASE_URL no definida, omitiendo migraciones."
fi

# Limpia caché para asegurar que la nueva versión la regenere
php bin/cake.php cache clear_all || true

exec "$@"
