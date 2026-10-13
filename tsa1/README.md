# Tasks for Today Management System

A small CodeIgniter 4 and MySQL application for viewing tasks scheduled today, browsing the full task schedule, and viewing one database-backed demo profile. It is structured using MVC: controllers coordinate requests, models retrieve database records, and views render pages.

## Features and routes

- `GET /` — welcome dashboard with today's date, task count, and tasks matching the current server date.
- `GET /tasks` — all tasks, ordered by scheduled date and creation time.
- `GET /profile` — the demo user's username, name, email, and account creation date.
- `GET /about` — static project and developer information.

Navigation is shared across the four responsive pages. Task and profile values are escaped in the views.

## Technology

- PHP 8.2 or newer
- CodeIgniter 4.7
- MySQL / MariaDB through CodeIgniter's MySQLi driver
- Custom responsive CSS; no additional UI framework

## Database setup

1. Start MySQL and create a database, for example:

   ```sql
   CREATE DATABASE tasks_for_today CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
   ```

2. Copy `env` to `.env` in the project root, then set these values (remove the leading `#`):

   ```ini
   CI_ENVIRONMENT = development
   app.baseURL = 'http://localhost:8080/'
   database.default.hostname = localhost
   database.default.database = tasks_for_today
   database.default.username = root
   database.default.password =
   database.default.DBDriver = MySQLi
   database.default.port = 3306
   ```

   Use the credentials configured for your local MySQL server. `.env` is local configuration and should not be committed.

3. From the project root, create the required tables and insert the sample records:

   ```sh
   php spark migrate
   php spark db:seed DemoSeeder
   ```

   The migration creates exactly the `tasks` and `users` tables. The task columns are `id`, `title`, `status`, `task_date`, and `created_at`; the user columns are `id`, unique `username`, `full_name`, `email`, and `created_at`. The seeder adds eight everyday/school tasks across dates relative to its run date (including today) and one fictional demo user. It checks for existing title/date pairs and username before inserting, so rerunning the seeder does not duplicate its sample records. It does not clear existing records.

## Run locally

Install project dependencies if needed, then start the CodeIgniter development server from the project root:

```sh
composer install
php spark serve
```

Open `http://localhost:8080/`. The pages are available at:

- `http://localhost:8080/`
- `http://localhost:8080/tasks`
- `http://localhost:8080/profile`
- `http://localhost:8080/about`

## Folder structure

```text
app/
  Config/Routes.php                 Explicit page routes
  Controllers/                      Home, Tasks, Profile, About
  Database/Migrations/               Required MySQL table definitions
  Database/Seeds/DemoSeeder.php      Relative-date sample data
  Models/                            TaskModel and UserModel
  Views/layout/                      Shared navigation and footer
  Views/pages/                       Four page templates
public/assets/css/app.css            Responsive visual styles
tests/                               CodeIgniter/PHPUnit starter tests
```

## Deployment

For hosting, use PHP 8.2+, enable the required CodeIgniter PHP extensions and MySQLi, configure production database credentials through the host environment, and point the web server document root at this project's `public/` directory. Run the migration and seeder against the intended database. Use HTTPS and production environment settings. No hosted URL or deployment is configured by this project.

## Verification

Run the existing PHPUnit suite with `composer test`. PHP syntax can be checked with `php -l` on the changed PHP files. The pages require the configured database and tables to render successfully.
