# Deploying to Hostinger (Shared / Premium / Business)

This app is **Laravel 13 + PHP 8.3**. Use **PHP hosting**, *not* Hostinger's
"Node.js" deployment — Node here is only a build tool for the frontend CSS/JS,
it is never run on the server.

There are two moving parts:
1. **Files** — the Laravel app, uploaded into `public_html/`.
2. **Database** — a MySQL database, imported from
   [`database/treasury-management.sql`](database/treasury-management.sql).

---

## 1. Build the frontend assets locally

Hostinger shared hosting can't run `npm`, so compile assets on your machine and
upload the result.

```bash
npm install
npm run build        # outputs to public/build/
```

Commit/keep the generated `public/build/` folder — it must be uploaded.

## 2. Install PHP dependencies (production)

If you have **SSH** (Business plan, or Premium with SSH enabled), skip this and
run it on the server in step 5. Otherwise run locally and upload `vendor/`:

```bash
composer install --no-dev --optimize-autoloader
```

## 3. Create the database in hPanel

1. hPanel → **Databases → Management** → create a new MySQL database + user.
2. Note the **database name, username, password** (Hostinger prefixes them,
   e.g. `u123456_treasury`).
3. Open **phpMyAdmin** for that database → **Import** tab →
   upload `database/treasury-management.sql` → **Go**.

The dump creates all tables and loads your data. Runtime tables
(`cache`, `sessions`, `jobs`, …) are intentionally empty.

## 4. Upload the files

Upload the whole project into `public_html/` (File Manager or SFTP), **including**:
`app/ bootstrap/ config/ database/ public/ resources/ routes/ storage/ vendor/`
plus `artisan`, `composer.json`, and the root **`.htaccess`** (already created).

**Do not upload:** `node_modules/`, `.git/`, `tests/`, your local `.env`,
`database/database.sqlite`.

> The root `.htaccess` forwards traffic into `public/`, so you don't need to
> change the document root or move files. (Cleaner option: if your plan lets you
> set the document root in hPanel, point it at `public_html/public` and delete
> the root `.htaccess`.)

## 5. Configure the server `.env`

1. Copy [`.env.production`](.env.production) to `.env` on the server.
2. Fill in every `CHANGE_ME`: `APP_URL`, the three `DB_*` values from step 3,
   and (optionally) mail settings.
3. Set `APP_KEY`:
   - With SSH: `php artisan key:generate`
   - Without SSH: copy the `APP_KEY=base64:...` value from your local `.env`.

If you have SSH, also run from `public_html/`:

```bash
composer install --no-dev --optimize-autoloader   # if you skipped step 2
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

> The DB is already imported, so you do **not** need `php artisan migrate`.
> If you'd rather build the schema on the server instead of importing the SQL,
> you could run `php artisan migrate --seed` — but importing the dump is the
> recommended path since it carries your existing data.

## 6. File permissions

These two folders must be writable by the web server:

```bash
chmod -R 775 storage bootstrap/cache
```

(In File Manager, set `storage/` and `bootstrap/cache/` to 775 recursively.)

## 7. SSL

Enable the free SSL certificate in hPanel → **Security → SSL**. The root
`.htaccess` already forces HTTPS once SSL is active. (If SSL isn't ready yet,
temporarily remove the "Force HTTPS" lines from `.htaccess`.)

## 8. Verify

- Visit `https://yourdomain.com` — the login page should load with styling
  (confirms `public/build/` assets uploaded correctly).
- Log in and click through a couple of modules.
- If you see a 500 error: temporarily set `APP_DEBUG=true` in `.env`, reload,
  read the message, fix, then set it back to `false`.

---

## Updating the site later

1. `npm run build` locally, re-upload `public/build/`.
2. Upload changed PHP files.
3. With SSH: `php artisan config:cache && php artisan view:cache`.
4. Schema changes: either re-import an updated SQL dump
   (`python database/sqlite_to_mysql.py` regenerates it) or run
   `php artisan migrate` on the server.
