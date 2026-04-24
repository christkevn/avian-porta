# ATC Materio (avian-atc)

ATC Materio is a Laravel-based training management application for organizing training schedules, courses (materi), modules, trainers, participants, and related settings. It includes models and features for trainings, modules, trainers, products, and branches.

## Quick overview

- **Framework**: Laravel
- **Language**: PHP
- **Primary features**: Training scheduling, participant management, course content, trainers, and admin settings

## Requirements

- PHP 8.0+ (match your Laravel version)
- Composer
- Node.js + npm (for frontend assets)
- MySQL or other supported database
- XAMPP (optional) or other local PHP server on Windows

## Local setup (Windows / PowerShell)

1. Clone repository and change into project directory (if not already):

 git clone <repo-url> && cd atc

2. Install PHP dependencies:

 composer install

3. Copy environment file and generate app key:

 cp .env.example .env; php artisan key:generate

 (On PowerShell you can use: `Copy-Item .env.example .env`)

4. Configure `.env` database settings (DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME, DB_PASSWORD).

5. Run database migrations and seeders:

 php artisan migrate --seed

6. Create the storage symlink for public files:

 php artisan storage:link

7. Install frontend dependencies and build assets:

 npm install
 npm run dev

8. Serve the application locally (or use XAMPP + virtual host):

 php artisan serve

 By default the app will be available at `http://127.0.0.1:8000`.

## Running tests

Run the test suite with:

```
php artisan test
```

## Common commands

- Install PHP dependencies: `composer install`
- Install Node dependencies: `npm install`
- Run migrations: `php artisan migrate`
- Run seeds: `php artisan db:seed`
- Create storage link: `php artisan storage:link`
- Run app: `php artisan serve`

## Project structure highlights

- `app/Models` — Eloquent models (Trainer, Materi, Modul, TrainingParticipants, etc.)
- `app/Http/Controllers` — Application controllers
- `resources/views` — Blade templates
- `routes/web.php` — Web routes

## Troubleshooting

- If Composer runs out of memory on Windows, try: `composer install --no-scripts --no-progress` and increase PHP memory_limit in `php.ini`.
- If assets don't compile, remove `node_modules` and run `npm install` again.
- If migrations fail, confirm DB credentials in `.env` and that the database exists and is accessible.

## Contributing

If you'd like to contribute, please fork the repository, create a branch for your change, and open a pull request. Keep changes focused and include tests where appropriate.

## License

This project is open source and available under the MIT License.
