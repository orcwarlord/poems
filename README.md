# PHP / MySQL environment

This project provides a PHP 8.3 poem library with a rich-text editor, Apache, and MySQL 8.4 through Docker Compose.

## Start

```sh
docker compose up --build
or?
```sh
docker compose up -d



Open <http://localhost:8080>. The response reports the PHP version and MySQL connection status.

## Structure

The application follows a small MVC layout:

- `src/app/Models/Poem.php` contains poem queries and persistence.
- `src/app/Controllers/PoemController.php` handles requests, validation, and redirects.
- `src/views/` contains dashboard, form, and poem detail templates.
- `src/index.php` and `src/poems/*.php` are thin route entry points.

## Stop

```sh
docker compose down
```

To remove the persisted database volume too:

```sh
docker compose down -v
```

Database connection defaults:

- Host: `127.0.0.1` from the host, `db` from the PHP container
- Port: `3307` from the host, `3306` from the PHP container
- Database: `app`
- User: `app`
- Password: `app`
- Root password: `root`

