# Book Reviews

A Laravel teaching case study for CC-Y2-AWD, built lesson by lesson
alongside a companion tutorial site.

## Docker Setup

Four Docker Compose services, in the same shape as the apache-php-mysql
environment used elsewhere in CC-Y1-WebDev. The Laravel app lives in
`src/`. There are two `.env` files — one beside `compose.yaml`, one in
`src/` — that must always be identical; **don't edit either one.**

| Service | What it is | URL |
|---|---|---|
| `apache-php-container` | Serves the Laravel app | http://localhost:8080 |
| `mysql-container` | MySQL 8 | `localhost:3306` |
| `phpmyadmin-container` | phpMyAdmin | http://localhost:8081 |
| `workspace-container` | Terminal for `php artisan`/`composer`/`npm` — no server of its own | — |

## First-time setup

1. `docker compose up -d --build`
2. Open a shell in the workspace container: `docker compose exec workspace-container bash`
3. Inside that shell, from `/var/www/html` (already the working dir):
   ```
   composer install
   npm install --ignore-scripts && npm run build
   php artisan migrate
   ```
   `vendor/` and `node_modules/` aren't committed (both gitignored), so
   `composer install` has to come first — everything after it needs
   `vendor/autoload.php`.
4. Visit http://localhost:8080.

**If `php artisan migrate` fails with `Access denied for user 'laravel'@'...'`:**
this means `docker/mysql/data/` (MySQL's on-disk data, bind-mounted and
gitignored) was already initialized by an earlier container start,
*before* whatever is currently in `DB_PASSWORD`. MySQL only applies the
`MYSQL_USER`/`MYSQL_PASSWORD`/`MYSQL_DATABASE` env vars the first time it
initializes an *empty* data directory — once that directory has data, a
later `.env` edit doesn't change the real stored password, so it no
longer matches what Laravel is sending. Fix by wiping the stale data and
letting MySQL reinitialize from the current `.env`:
```
docker compose rm -sf mysql-container
# delete the contents of docker/mysql/data/ (safe — it's gitignored, dev-only)
docker compose up -d mysql-container
```
Then re-run `php artisan migrate`.
