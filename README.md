# KDO Apps — Pengelolaan Kendaraan Dinas Operasional

Laravel 13 · REST API `/api/v1` (Sanctum) · Frontend Blade + Bootstrap 4 + DataTables

```bash
composer install && cp .env.example .env && php artisan key:generate
php artisan migrate:fresh --seed
php artisan serve
```

Login: `admin@kdo-apps.test` / `password` (admin), `user@kdo-apps.test` / `password` (user).

Dokumentasi lengkap ada di [docs/](docs/README.md): instalasi, keputusan teknis, skema database, API, dan log pengembangan.
