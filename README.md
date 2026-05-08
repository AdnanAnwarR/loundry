# 🚀 CodeIgniter 4 — Project Setup Guide

Panduan lengkap cara setup project CodeIgniter 4 dari awal hingga bisa berjalan di local environment.

---

## 📋 Prasyarat (Prerequisites)

Pastikan software berikut sudah terinstall di komputer kamu:

| Software           | Versi Minimum | Download                         |
| ------------------ | ------------- | -------------------------------- |
| PHP                | >= 8.2        | https://www.php.net/downloads    |
| Composer           | >= 2.x        | https://getcomposer.org/download |
| Git                | Latest        | https://git-scm.com              |
| MySQL / PostgreSQL | Latest        | https://www.mysql.com            |

> [!WARNING]
> PHP 8.1 telah end-of-life sejak 31 Desember 2025. Pastikan menggunakan PHP 8.2+.

---

## 📥 Langkah 1 — Clone Repository

Clone project dari GitHub ke komputer lokal kamu:

```bash
git clone https://github.com/username/nama-project.git
```

Masuk ke folder project:

```bash
cd nama-project
```

---

## 📦 Langkah 2 — Install Dependencies PHP

Install semua package PHP yang dibutuhkan menggunakan Composer:

```bash
composer install
```

> Jika kamu berada di environment production, gunakan:
>
> ```bash
> composer install --optimize-autoloader --no-dev
> ```

---

## ⚙️ Langkah 3 — Konfigurasi Environment

Salin file `env` menjadi `.env`:

```bash
cp env .env
```

> **Windows:**
>
> ```bash
> copy env .env
> ```

Buka file `.env` dan ubah environment menjadi `development`:

```env
CI_ENVIRONMENT = development
```

---

## 🌐 Langkah 4 — Konfigurasi Base URL

Masih di file `.env`, sesuaikan base URL aplikasi kamu:

```env
app.baseURL = 'http://localhost:8080/'
```

---

## 🗄️ Langkah 5 — Konfigurasi Database

Sesuaikan konfigurasi database di file `.env`:

```env
database.default.hostname = localhost
database.default.database = nama_database_kamu
database.default.username = root
database.default.password = password_kamu
database.default.DBDriver = MySQLi
database.default.port     = 3306
```

Buat database baru di MySQL sesuai nama yang kamu tulis di `database.default.database`.

---

## 🔄 Langkah 6 — Jalankan Migration

Jalankan migration untuk membuat tabel di database:

```bash
php spark migrate
```

Jika ingin mengisi data awal (seeder):

```bash
php spark db:seed NamaSeeder
```

---

## ▶️ Langkah 7 — Jalankan Development Server

Jalankan built-in server CodeIgniter:

```bash
php spark serve
```

Aplikasi kamu sekarang bisa diakses di:

```
http://localhost:8080
```

Untuk menggunakan port lain:

```bash
php spark serve --port 8081
```

---

## 📁 Struktur Folder Penting

```
nama-project/
├── app/            # Kode aplikasi (Controllers, Models, Views, dll)
├── public/         # Document root — arahkan web server ke sini
├── vendor/         # Dependency Composer
├── writable/       # Cache, logs, session (pastikan permission writable)
├── .env            # Konfigurasi environment
└── spark           # CLI tool CodeIgniter
```

---

## ⚡ Perintah Spark yang Sering Digunakan

```bash
php spark serve                  # Jalankan development server
php spark make:controller Nama   # Buat controller baru
php spark make:model Nama        # Buat model baru
php spark make:migration Nama    # Buat file migrasi
php spark migrate                # Jalankan migrasi database
php spark migrate:rollback       # Rollback migrasi
php spark db:seed NamaSeeder     # Jalankan seeder
php spark list                   # Lihat semua perintah yang tersedia
```

---

## ❗ Troubleshooting

**Error: `php spark` tidak dikenali**

- Pastikan PHP sudah ditambahkan ke PATH sistem kamu.

**Error: `composer` tidak dikenali**

- Pastikan Composer sudah terinstall dan PATH sudah dikonfigurasi.

**Error: `SQLSTATE[HY000] [1045] Access denied`**

- Periksa kembali `database.default.username` dan `database.default.password` di file `.env`.

**Halaman menampilkan error 404**

- Pastikan `app.baseURL` di `.env` sudah sesuai dengan URL yang kamu gunakan.

**Folder `writable` tidak bisa diakses**

- Ubah permission folder `writable/`:
  ```bash
  chmod -R 777 writable/
  ```

---

## 📄 Lisensi

Project ini menggunakan lisensi [MIT](https://opensource.org/licenses/MIT).
