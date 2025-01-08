# This page provide and installation guide for SmartCms Core package.

---

## Installation

To install the SmartCms Core package, follow these steps:

### 1. Install fresh laravel project with version 11.x or higher:

```bash
composer create-project --prefer-dist laravel/laravel my-project
```

### 2. Install the SmartCms Core package:

```bash
composer require smart-cms/core
```

### 3. Update the `.env` file with your database credentials:

```bash
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=my_database
DB_USERNAME=my_username
DB_PASSWORD=my_password
```

If you want to set a custom admin password for default user, you can add `ADMIN_PASSWORD` to `.env` file.

```bash
ADMIN_PASSWORD=my_password
```

### 4. Run the installation command of the SmartCms Core package:

```bash
php artisan scms:install
```

## Now you can access the admin panel by visiting `/admin` route of your application.

---

## Next Steps

-   [Update](update.md) Update the SmartCms Core package to the latest version.
-   [Variables](variables.md) Define and manage variables for your content.
-   [Layouts](layouts.md) Create and manage layouts for your pages.
-   [Sections](sections.md) Create and manage sections for your website.
-   [Pages](pages.md) Create and manage pages for your website.
-   [Events](events.md) Modify, extend, or hook into the functionality provided by the package.
-   [Overview](overview.md) Overview of the SmartCms Core package.
