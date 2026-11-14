<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## Panduan proyek

### Menjalankan aplikasi lokal

```powershell
composer install
Copy-Item .env.example .env
php artisan key:generate
php artisan migrate
php artisan serve
```

Swagger UI tersedia di `http://localhost:8000/api/documentation`.

Seeder hanya membuat data demo di environment `local` atau `testing`. Buku demo dibuat tanpa syarat; akun admin demo hanya dibuat jika `SEED_ADMIN_EMAIL` dan `SEED_ADMIN_PASSWORD` sudah diatur di `.env`. Di lokal, seeder memperbarui kata sandi admin yang emailnya sama dengan nilai yang dikonfigurasi. Untuk merotasi akun demo lama, atur kedua nilai itu lalu jalankan `php artisan db:seed`. Jangan memakai kredensial demo di produksi.

### Menjalankan tes di Windows

Jika ekstensi SQLite belum diaktifkan di PHP Laragon, jalankan:

```powershell
powershell -ExecutionPolicy Bypass -File .\scripts\test.ps1
```

Skrip mengaktifkan ekstensi SQLite hanya untuk proses tes, menjalankan `php artisan test` dengan database in-memory, lalu membersihkan konfigurasi sementaranya.

### Checklist sebelum deploy

- Atur `APP_ENV=production`, `APP_DEBUG=false`, `APP_KEY` unik, URL HTTPS, dan kredensial database produksi melalui environment deployment yang aman. Jangan commit `.env` atau rahasia.
- Atur `APP_URL` ke domain HTTPS API produksi. `L5_SWAGGER_CONST_HOST` mengikuti `APP_URL` secara default; jika diatur terpisah, nilainya harus berupa domain API produksi yang benar.
- Jangan jalankan seeder demo di produksi. Seeder ini menolak membuat data di luar `local` dan `testing`.
- Set `L5_SWAGGER_GENERATE_ALWAYS=false` di produksi dan jalankan `php artisan l5-swagger:generate` saat proses deploy. Swagger UI (`/api/documentation`) dan JSON docs (`/docs`) memakai HTTP Basic Authentication di produksi.
- Token Sanctum berlaku selama 24 jam (1440 menit); pengguna perlu login ulang setelah masa berlaku token habis.
- Gunakan MySQL atau PostgreSQL untuk produksi dan simpan kredensialnya di environment deployment yang aman.
- Ambil dan verifikasi backup database sebelum migrasi produksi; uji pemulihan backup secara berkala. Jalankan migrasi yang sudah ditinjau dengan `php artisan migrate --force`.
- Jangan menjalankan `php artisan migrate:fresh` di produksi karena perintah itu menghapus seluruh tabel dan data.
- Jalankan tes sebelum deploy, lalu verifikasi health endpoint, autentikasi, dan migrasi di lingkungan staging.

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com/)**
- **[Tighten Co.](https://tighten.co)**
- **[WebReinvent](https://webreinvent.com/)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel/)**
- **[Cyber-Duck](https://cyber-duck.co.uk)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Jump24](https://jump24.co.uk)**
- **[Redberry](https://redberry.international/laravel/)**
- **[Active Logic](https://activelogic.com)**
- **[byte5](https://byte5.de)**
- **[OP.GG](https://op.gg)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
