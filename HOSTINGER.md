# Hostinger deployment

This project is ready for a standard Laravel deployment on Hostinger shared hosting.

## Recommended layout

Place the application outside `public_html` and point the domain document root to the app's `public` directory.

Example:

```text
/home/u123456789/storenav
/home/u123456789/storenav/public
```

If your Hostinger plan lets you set a custom document root, use the `public` folder directly.
If it does not, move the contents of `public/` into `public_html/` and adjust `public_html/index.php` to point back to the project root.

## Production env

Use `HOSTINGER.env.example` as the starting point for the server `.env`.

## Server steps

1. Upload the project files.
2. Create a MySQL database in Hostinger.
3. Copy `HOSTINGER.env.example` to `.env` and fill in the real values.
4. Run Composer install on the server if available, or upload the built `vendor/` directory.
5. Run the database migrations and seeder.
6. Run `php artisan storage:link`.
7. Clear and cache config after the `.env` is in place.

## Cron

Add a cron entry for Laravel scheduler if you use scheduled tasks:

```bash
* * * * * /usr/bin/php /home/u123456789/storenav/artisan schedule:run >> /dev/null 2>&1
```

If you keep the queue connection set to `sync`, you do not need a separate queue worker on Hostinger shared hosting.

