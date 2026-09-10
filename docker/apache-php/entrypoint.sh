#!/bin/sh
# storage/ and bootstrap/cache/ need to be writable by www-data (the user
# Apache serves requests as), but they're usually created root-owned —
# by `composer create-project`/`artisan` run as root in workspace-container,
# or simply by whichever OS user owns the bind-mounted src/ on a student's
# host. Fixing permissions here, on every container start, means it self-heals
# regardless of how those files ended up root-owned, rather than relying on
# a one-off `chmod` that a later rebuild or a fresh clone would undo.
if [ -d /var/www/html/storage ]; then
    chmod -R ugo+rwX /var/www/html/storage /var/www/html/bootstrap/cache 2>/dev/null || true
fi

exec "$@"
