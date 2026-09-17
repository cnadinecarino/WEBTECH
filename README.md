# TFA1 Simple POS

TFA1 is a basic four-page Point-of-Sale website built with CodeIgniter 4 for a
Web Technology laboratory activity. It demonstrates routing, controllers, views,
shared navigation, and passing static array data from controllers to views.

The project does **not** use a database, API, authentication, migrations, or a
database model. Customer and staff data are fictional records stored in static PHP
arrays for demonstration purposes.

## Requirements

- PHP 8.2 or later
- Composer
- Required PHP extensions for CodeIgniter 4, including `intl` and `mbstring`
- A web server such as Apache (included with XAMPP), or CodeIgniter's local server

## Installation

The project has already been created with Composer. For a fresh copy, open a
terminal in the folder that should contain the project and run:

```bash
composer create-project codeigniter4/appstarter TFA1
cd TFA1
composer install
```

For this existing project, restore missing dependencies by running `composer install`
from the `TFA1` directory.

## Environment configuration

If `.env` does not exist, copy the supplied `env` template to `.env`. Then enable
development mode and set the URL that matches the local setup:

```ini
CI_ENVIRONMENT = development
app.baseURL = 'http://localhost/WEBTECH/TFA1/'
```

The base URL must end with a slash. The supplied root `.htaccess` forwards requests
to CodeIgniter's `public` directory when the project is opened through XAMPP.

Use `CI_ENVIRONMENT = production` on a real production server so detailed errors are
not shown publicly.

## Running the application

### XAMPP Apache

1. Place `TFA1` inside `xampp/htdocs/WEBTECH`.
2. Start Apache in the XAMPP Control Panel.
3. Open `http://localhost/WEBTECH/TFA1/` in a browser.

### CodeIgniter development server

From the `TFA1` directory, run:

```bash
php spark serve
```

The terminal displays the local address, normally `http://localhost:8080`. If using
this method, temporarily set `app.baseURL` to that displayed address with a trailing
slash.

## Routes

| URL | Controller method | Purpose |
| --- | --- | --- |
| `/` | `Pages::index` | Landing page |
| `/about` | `Pages::about` | Project and POS information |
| `/customers` | `Customers::index` | Five fictional customer records |
| `/users` | `Users::index` | Five fictional staff records |

Only these four application routes are defined. The default welcome route is no
longer active.

## MVC structure

- `app/Config/Routes.php` maps each URL to a controller method.
- `app/Controllers/Pages.php` prepares the two informational pages.
- `app/Controllers/Customers.php` contains the static customer array.
- `app/Controllers/Users.php` contains the static staff array.
- `app/Views/` contains the shared layout and four page views.
- The customer and user views use `foreach` loops and `esc()` to safely display
  every record passed by their controllers.

## Browser testing

After starting the server, visit each route:

1. `/` displays the POS welcome page.
2. `/about` explains the project and its static data source.
3. `/customers` displays five customers with names, emails, and phone numbers.
4. `/users` displays five staff accounts with usernames, names, and roles.
5. On every page, test the Home, About, Customer Accounts, and User Accounts links.

No hosted deployment is claimed for this project.
