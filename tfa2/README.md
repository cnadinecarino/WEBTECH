# Simple POS TFA2

Simple POS is a CodeIgniter 4 laboratory application that demonstrates a complete database-backed MVC flow. TFA2 preserves the four TFA1 pages and replaces the Customer Accounts and User Accounts controller arrays with records retrieved from MySQL through CodeIgniter Models.

## Features

- Home, About, Customer Accounts, and User Accounts pages
- `CustomerModel` and `UserModel` using CodeIgniter Model and Query Builder
- Exact laboratory schema created through a migration
- Rerunnable sample-data seeder with five fictional records per table
- Escaped database output, nullable-phone handling, empty states, deterministic ID ordering, and consistent date display
- Shared responsive layout with accessible navigation and tables

## Prerequisites

- PHP 8.2 or later with `intl`, `mbstring`, and `mysqli`
- Composer 2
- MySQL 5.7+ or MariaDB 10.4+
- A MySQL account allowed to create and use the project database

The project currently targets CodeIgniter 4.7 through Composer.

## Install dependencies

From the project root:

```bash
composer install
```

## Create the database

Create an empty project-specific database. This does not remove or alter an existing database:

```sql
CREATE DATABASE IF NOT EXISTS tfa2_pos
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_general_ci;
```

With the XAMPP command-line client on Windows, the equivalent command is:

```powershell
& 'C:\Users\YOUR_NAME\xampp\mysql\bin\mysql.exe' -u root -p -e "CREATE DATABASE IF NOT EXISTS tfa2_pos CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;"
```

## Configure `.env`

Copy the tracked example and edit the copy:

```powershell
Copy-Item env .env
```

Set the local values in `.env`:

```ini
CI_ENVIRONMENT = development
app.baseURL = 'http://localhost:8080/'
app.indexPage = ''

database.default.hostname = localhost
database.default.database = tfa2_pos
database.default.username = YOUR_MYSQL_USERNAME
database.default.password = YOUR_MYSQL_PASSWORD
database.default.DBDriver = MySQLi
database.default.DBPrefix =
database.default.port = 3306
```

`.env` is excluded by `.gitignore`; do not commit credentials. The tracked `env` file contains placeholders only.

## Create the schema and sample data

Run the migration and then the idempotent seeder:

```bash
php spark migrate
php spark db:seed PosAccountSeeder
```

The migration creates exactly these application fields:

```sql
CREATE TABLE customers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    created_at DATETIME NOT NULL
);

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    full_name VARCHAR(100) NOT NULL,
    created_at DATETIME NOT NULL
);
```

The seeder checks each customer identity and each unique username before inserting, so rerunning it does not delete existing records or duplicate its samples.

## Run locally

```bash
php spark serve
```

Open:

- Home: `http://localhost:8080/`
- About: `http://localhost:8080/about`
- Customer Accounts: `http://localhost:8080/customers`
- User Accounts: `http://localhost:8080/users`

## MVC data flow

The route selects a controller. The controller creates the appropriate Model and requests rows ordered by ascending ID. The Model uses CodeIgniter's database layer and Query Builder to retrieve MySQL records as arrays. The controller passes those arrays to the existing view variable (`$customers` or `$users`), and the view escapes and renders each value.

```text
Route -> Controller -> Model -> MySQL -> Controller -> View
```

Views never connect to MySQL, and the controllers contain no SQL or fallback arrays. A database failure remains visible as an application error rather than being disguised as an empty table.

## Verification commands

```bash
php spark routes
php spark migrate:status
php spark db:table customers
php spark db:table users
vendor\bin\phpunit
```

After starting the server, visit all four URLs and confirm that navigation works, both listings contain database rows, the customer with no phone shows `Not provided`, and no PHP errors appear.

## Common setup issues

- **Unknown database:** Create `tfa2_pos` before running migrations.
- **Access denied:** Correct the `.env` username/password and grant that account access to `tfa2_pos`.
- **Connection refused:** Start MySQL in XAMPP and verify the configured host and port.
- **Missing `mysqli`:** Enable `extension=mysqli` in the active `php.ini`, then restart PHP/Apache.
- **Base URL or CSS is wrong:** Set `app.baseURL` to the exact local or hosted public URL, including the trailing slash.
- **Schema conflict:** Do not overwrite populated tables. Use a fresh database, inspect the differences, and migrate data deliberately.

## Hosting and submission

For production hosting:

1. Point the web server document root to `public/`.
2. Run `composer install --no-dev --optimize-autoloader` on the host.
3. Create a production MySQL database and database user.
4. Supply an untracked production `.env` with `CI_ENVIRONMENT = production`, the HTTPS base URL, and production database credentials.
5. Run `php spark migrate --all`, followed by `php spark db:seed PosAccountSeeder` only if demonstration data is required.
6. Make `writable/` writable by the web-server account.
7. Verify `/`, `/about`, `/customers`, and `/users` over HTTPS and confirm that assets and database listings load.

No deployment or hosted URL is claimed by this repository. A live rubric submission still requires an authorized hosting account, production credentials, and a verified public URL matching this code.
