# Simple POS (TFA1)

Simple POS is a basic four-page Point-of-Sale website created as a CodeIgniter 4
laboratory activity. It demonstrates routes, controllers, views, shared navigation,
and controller-to-view data passing with no database.

## Requirements

- PHP 8.2 or newer
- Composer
- Required CodeIgniter PHP extensions, including `intl` and `mbstring`

## Installation

For a new copy of the CodeIgniter starter project, run:

```bash
composer create-project codeigniter4/appstarter TFA1
cd TFA1
composer install
```

This TFA1 folder is already installed. On another computer, running `composer install`
restores the dependencies recorded in `composer.lock`.

## Environment configuration

The active `.env` file uses development mode and CodeIgniter's local server URL:

```ini
CI_ENVIRONMENT = development
app.baseURL = 'http://localhost:8080/'
app.indexPage = ''
```

If another host or port is used, update `app.baseURL` so generated navigation and
asset URLs match it. No database settings are required or used.

## Running the application

From the TFA1 project folder, start the development server:

```bash
php spark serve
```

Then open `http://localhost:8080/` in a browser. Keep the terminal running while
testing the application.

## Required routes

| URL | Controller method | Purpose |
| --- | --- | --- |
| `/` | `Pages::index` | POS landing page |
| `/about` | `Pages::about` | Project and MVC information |
| `/customers` | `Customers::index` | Customer account listing |
| `/users` | `Users::index` | User/staff account listing |

Only explicit GET routes are used for the four required pages. The default welcome
route has been replaced.

## MVC structure

- `app/Config/Routes.php` maps each URL to a controller method.
- `app/Controllers/Pages.php` prepares the landing and about pages.
- `app/Controllers/Customers.php` contains five fictional customer records.
- `app/Controllers/Users.php` contains five fictional staff records.
- `app/Views/` renders the pages, shared navigation, and footer.
- `public/assets/css/style.css` provides the responsive page styling.

Customer and user data are temporary static PHP arrays declared in their respective
controllers. The controllers pass the arrays to the views, and each listing view uses
a `foreach` loop with CodeIgniter's `esc()` function. The application has no model,
database query, migration, or database connection.

## Browser testing

1. Visit `/` and confirm the welcome text and both action buttons appear.
2. Visit `/about` and confirm the project and no-database explanation appears.
3. Visit `/customers` and confirm five customers and all three columns appear.
4. Visit `/users` and confirm five staff members and all three columns appear.
5. On every page, use Home, About, Customer Accounts, and User Accounts in the navigation.
6. Resize the browser to confirm the layout remains usable on a narrow screen.
7. Confirm there are no PHP exceptions, missing views, or broken stylesheet requests.

The project has not been described as hosted or deployed. For classroom testing, use
the local development server above.
