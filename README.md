# FocusFlow

FocusFlow is a Microsoft To Do inspired task management web application built with Laravel 12, Blade, MySQL, Tailwind CSS, and Alpine.js. It supports personal task organization as well as a lightweight team workflow where a team leader can create members and assign work across shared folders.

## Highlights

- Clean Microsoft To Do style dashboard with sidebar + task workspace
- Folder based task organization with task counts
- Smart views for `All Tasks`, `Today`, `Important`, and `Completed`
- Numeric priority sorting where `0` is always shown first
- Team leader and team member workflow
- Laravel authentication with user scoped data access
- Seed data for a ready-to-demo interface

## Tech Stack

- Laravel 12
- PHP 8.2+
- Blade templating
- MySQL
- Tailwind CSS
- Alpine.js

## Core Rules

- Task priorities use numeric values:
  - `0` = highest
  - `1` = high
  - `2` = medium
  - `3` = low
- Tasks are always sorted by:
  1. `priority` ascending
  2. `created_at` descending for same priority

## Team Workflow

- A leader owns a workspace
- The leader can create team members
- The leader can create folders and tasks
- The leader can assign tasks to members
- Members only see tasks assigned to them
- Members can update task completion status

## Local Setup

1. Clone the repository.
2. Install PHP dependencies:

```bash
composer install
```

3. Install frontend dependencies:

```bash
npm install
```

4. Copy the environment file and update database credentials:

```bash
cp .env.example .env
```

On Windows PowerShell you can also run:

```bash
copy .env.example .env
```

5. Generate the app key:

```bash
php artisan key:generate
```

6. Run migrations and seed demo data:

```bash
php artisan migrate --seed
```

7. Start the backend server:

```bash
php artisan serve
```

8. Start Vite during development:

```bash
npm run dev
```

For a production-like asset build instead of Vite dev server:

```bash
npm run build
```

## Demo Accounts

Seeded demo credentials:

- Leader: `demo@focusflow.test` / `password`
- Member: `sadia@focusflow.test` / `password`
- Member: `rafi@focusflow.test` / `password`

## Environment Notes

The included `.env.example` is prepared for a MySQL setup with:

- database name: `focusflow`
- host: `127.0.0.1`
- port: `3306`
- session driver: `database`
- queue connection: `database`
- cache store: `database`

Update these values before running migrations on your machine or server.

## Useful Commands

```bash
php artisan migrate
php artisan db:seed --class=FocusFlowSeeder
php artisan test
vendor/bin/pint
npm run build
```

## Deployment Checklist

1. Create a production `.env` file with real database and mail credentials.
2. Set `APP_ENV=production` and `APP_DEBUG=false`.
3. Run:

```bash
composer install --no-dev --optimize-autoloader
npm install
npm run build
php artisan key:generate
php artisan migrate --force
php artisan db:seed --class=FocusFlowSeeder --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

4. Point your web server document root to the `public/` directory.
5. Make sure the `storage/` and `bootstrap/cache/` directories are writable.
6. Configure a queue worker if you plan to use queued jobs in production.

## Project Structure

- `app/Models` contains `User`, `Folder`, and `Task`
- `app/Http/Controllers` contains dashboard, folder, task, and team member flows
- `app/Http/Requests` contains validation rules
- `app/Policies` contains ownership and workspace access rules
- `database/migrations` contains schema changes
- `database/seeders/FocusFlowSeeder.php` provides demo data
- `resources/views/focusflow` contains the main UI

## License

This project is open-sourced under the MIT license.
