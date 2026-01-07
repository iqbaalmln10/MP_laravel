# Struktur Folder — MP_laravel

_Di-generate: 2026-01-07_

Berikut adalah snapshot struktur folder dan file utama pada proyek `MP_laravel`.

---

## Root
- `artisan`
- `composer.json` / `composer.lock`
- `package.json` / `package-lock.json`
- `phpunit.xml`
- `vite.config.js`
- `README.md`
- `.gitignore`, `.gitattributes`, `.env.example`, `.editorconfig`

## app/
- Actions/
  - Fortify/
    - `CreateNewUser.php`
    - `ResetUserPassword.php`
    - `PasswordValidationRules.php`
- Livewire/
  - Actions/
    - `Logout.php`
- Http/
  - Controllers/
    - `Controller.php`
- Models/
  - `User.php`
  - `Project.php`
- Providers/
  - `AppServiceProvider.php`
  - `FortifyServiceProvider.php`
  - `VoltServiceProvider.php`

## bootstrap/
- `app.php`
- `providers.php`
- cache/ (`.gitignore`)

## config/
- `app.php`, `auth.php`, `cache.php`, `database.php`, `filesystems.php`, `fortify.php`, `logging.php`, `mail.php`, `queue.php`, `services.php`, `session.php`

## database/
- `mp_laravel.sql`
- `factories/` (`UserFactory.php`, `ProjectFactory.php`)
- `migrations/` (several migration files)
- `seeders/` (`DatabaseSeeder.php`)

## public/
- `index.php`, `robots.txt`, favicons, `.htaccess`
- `build/` (`manifest.json`, `assets/`)

## resources/
- `css/` (`app.css`)
- `js/` (`app.js`)
- `views/` (dashboard, welcome, components, flux, livewire, partials)

## routes/
- `web.php`, `console.php`

## storage/
- `app/`, `framework/`, `logs/` (with `.gitignore` files in subfolders)

## tests/
- `Pest.php`, `TestCase.php`
- `Unit/` and `Feature/` (with auth and settings tests)

## .github/
- `workflows/` (`tests.yml`, `lint.yml`)

---

Catatan:
- `vendor/` ada di repo (dependencies), tidak dirinci di sini.
- Jika Anda ingin, saya bisa ekspor struktur ini sebagai JSON/CSV, atau menambahkan detail package (`composer` / `npm`) ke dokumentasi.
