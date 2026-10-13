# TFA1 POS Account Manager

TFA1 is a small point-of-sale account management application for a Web Technology
laboratory activity. It uses CodeIgniter 4.7 and PHP 8.2, with customer and staff
account records stored in MySQL. It provides customer and user listings, create and
edit forms, server-side validation, CSRF protection, unique usernames, and optional
prepared user profile pictures. The starter project has no login or password
authentication; the user accounts are directory records, so no password fields or
authentication flow are added here.

## Requirements

- PHP 8.2 or later with `intl`, `mbstring`, `mysqli` or `pdo_mysql`, `fileinfo`, and
  GD enabled. GD is required to validate and prepare uploaded images.
- Composer and Apache with `mod_rewrite`, `mod_headers`, and `mod_mime` for the
  included XAMPP setup and upload-directory rules.
- MySQL or MariaDB with InnoDB support.
- Dependencies are listed in `composer.json`; the installed framework is CodeIgniter
  4.7.4 and PHPUnit 10.5 is included for development.

## Installation and environment

Install dependencies from this directory if needed:

```sh
composer install
```

Copy `env.example` to `.env` for a fresh local installation. Set `CI_ENVIRONMENT`,
`app.baseURL` (with a trailing slash), and the `database.default.*` values for your
local database. For example, XAMPP defaults may use `127.0.0.1`, database `tfa1`,
username `root`, and an empty password. Create that database in MySQL first. Keep
`.env` and all credentials out of Git; configure production values directly in the
deployment environment and set `CI_ENVIRONMENT = production`.

Do not replace an existing `.env` blindly. This repository intentionally excludes
environment files from version control.

## Database migrations

Back up an existing database before applying migrations. From the project directory,
run:

```sh
php spark migrate --all
```

The migrations create the customer and user tables with the original sample entries
only when those tables are absent, check existing users for username collisions using
the table's collation, add a unique username index, and add a nullable `users.avatar`
column. They do not delete or rename existing account records. If duplicate usernames
exist, the unique-index migration stops and reports the conflicting IDs for manual
review. Account migrations intentionally refuse automatic rollback to protect records;
use a database backup or a separately reviewed forward migration to undo schema work.

## Run locally

With XAMPP, put this project under `htdocs`, start Apache and MySQL, and open the URL
matching `app.baseURL`. The web document root should point to this project's `public`
directory; the included root rewrite supports the current nested XAMPP lab location.

Alternatively, from the project directory run:

```sh
php spark serve
```

Set `app.baseURL` to the displayed local address with a trailing slash. Do not expose
the development server publicly.

## Routes

| Method | Route | Purpose |
| --- | --- | --- |
| GET | `/` | POS home |
| GET | `/about` | Activity information |
| GET | `/customers` | Customer listing |
| GET | `/customers/new` | New customer form |
| POST | `/customers` | Create customer |
| GET | `/customers/{id}/edit` | Edit customer form |
| POST | `/customers/{id}` | Update customer |
| GET | `/users` | User accounts listing |
| GET | `/users/new` | New user form |
| POST | `/users` | Create user |
| GET | `/users/{id}/edit` | Edit user form and optional avatar upload |
| POST | `/users/{id}` | Update user |

State-changing forms use POST and CodeIgniter CSRF tokens. Missing records return a
404 response. Customer full name and email, and user username and full name, are
required; user usernames are unique according to the database collation. Role and
customer phone are optional fields retained from the existing account schema.

## Avatar uploads

Optional user avatars can be uploaded while editing a user account. The server accepts
JPG/JPEG and PNG files up to 2048 KB (2 MB), validates the upload and decoded image,
then uses CodeIgniter's GD Image service to crop proportionally to a centered 256 by
256 PNG. Generated filenames are random; the database stores only the filename.
Original uploads are never published. With no replacement selected, an existing
avatar is retained. An old managed image is deleted only after its replacement is
committed successfully. Missing files and empty avatar fields display the bundled
`public/assets/avatar-placeholder.svg`.

The application writes prepared files to `public/uploads/avatars/`. Ensure the PHP
process can create and write this directory in deployment (normally directory mode
`0755` with the web-server user as owner; use the narrowest permissions your host
supports). Do not make the directory broadly writable. Apache must allow the provided
`public/uploads/avatars/.htaccess` rules: directory listing and CGI are disabled,
script handlers are removed, and only correctly named generated PNG files are served.
If `.htaccess` overrides are disabled, copy equivalent deny-execution and allow-only
image rules into the virtual host configuration before enabling uploads. Prefer serving
only this dedicated image directory as static content, with script execution disabled.

## Manual verification checklist

1. Open the home, About, Customer Accounts, and User Accounts pages; try navigation at
   phone and desktop widths.
2. Create a customer and a user; confirm each appears in its listing.
3. Submit whitespace-only required fields, an invalid customer email, and a duplicate
   username; confirm field messages and preserved text.
4. Edit records and retain the current username; verify a different user's username
   is rejected and the selected row alone changes.
5. Upload valid JPG and PNG files, then try a corrupt image, a disguised non-image,
   and a file over 2048 KB. Confirm valid files produce square thumbnails and failures
   keep account data and any current avatar.
6. Save an edit with no selected replacement; confirm the current avatar remains.
7. Confirm users without an avatar and users with a missing avatar file show the
   placeholder. Check an unknown record ID returns 404.

The PHPUnit unit suite can be run with:

```sh
vendor/bin/phpunit
```

`tests/workflow-smoke.php` exercises HTTP forms and database changes only against its
dedicated disposable MySQL database. Set `TFA1_SMOKE_DATABASE=tfa1_verification`,
configure its loopback server and test database connection variables as documented
at the top of that script, and ensure the database contains the migrated schema before
running it. Do not point the workflow script at production or a database with records
to preserve.

## Deployment

No deployment is performed by this repository setup. Set the public HTTPS base URL,
production environment, dedicated database credentials, and required PHP extensions
in the host's environment. Point the web root at `public/`, enable the rewrite and
upload safeguards above, grant the PHP process only the required avatar-directory
write access, and run `php spark migrate --all` during a backed-up deployment. Verify
the configured routes and upload behavior on the host before accepting real account
data. No repository or hosted URL is assumed by this README.
