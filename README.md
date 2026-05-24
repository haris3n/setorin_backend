# Setorin Backend - Project Setup Guide

Repositori ini adalah backend untuk platform **Setorin**. Ikuti langkah-langkah di bawah ini untuk melakukan setup project di lingkungan lokal setelah melakukan `git clone`.

---

## 🚀 Langkah-Langkah Setup Lokal

### 1. Clone Repositori
Jika belum melakukan clone, jalankan perintah berikut di terminal Anda:
```bash
git clone git@github.com:haris3n/setorin_backend.git
cd setorin_backend


### 2. Install Dependencies (Composer)

Folder `vendor` diabaikan oleh Git, sehingga Anda perlu mengunduh semua package PHP yang dibutuhkan oleh Laravel:

```bash
composer install

```

*Pastikan versi PHP di komputer Anda sesuai dengan spesifikasi yang ditentukan di `composer.json`.*

### 3. Salin Konfigurasi Environment (`.env`)

Salin file template `.env.example` menjadi `.env` untuk konfigurasi lokal Anda:

* **Linux / macOS:**

```bash
cp .env.example .env

```

* **Windows (Command Prompt):**

```cmd
copy .env.example .env

```

* **Windows (PowerShell):**

```powershell
cp .env.example .env

```

### 4. Generate Application Key

Generate key enkripsi unik untuk aplikasi Laravel Anda:

```bash
php artisan key:generate

```

### 5. Konfigurasi Database

1. Buat database baru (kosong) di MySQL/DBMS Anda (misalnya dengan nama `setorin_backend` atau `setorin`).
2. Buka file `.env` yang baru dibuat menggunakan text editor (VS Code, dll).
3. Sesuaikan baris berikut dengan kredensial database lokal Anda:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nama_database_anda
DB_USERNAME=username_database_anda
DB_PASSWORD=password_database_anda

```

### 6. Jalankan Migrasi & Seeder Database

Buat struktur tabel beserta data awal/dummy (jika tersedia) ke dalam database:

```bash
php artisan migrate --seed

```

*Jika tidak membutuhkan data dummy, cukup jalankan `php artisan migrate`.*

### 7. Jalankan Server Lokal

Aplikasi backend sekarang siap dijalankan:

```bash
php artisan serve

```

Backend Anda akan berjalan di [http://127.0.0.1:8000](https://www.google.com/search?q=http://127.0.0.1:8000).

---

## 🛠️ Troubleshooting (Masalah yang Sering Muncul)

* **Error Authentication (JWT / Passport):** Jika project ini menggunakan JWT atau Passport untuk autentikasi API, pastikan untuk men-generate key terkait:

```bash
# Jika menggunakan JWT
php artisan jwt:secret

# Jika menggunakan Passport
php artisan passport:install

```

* **Error Folder Permission (Khusus Linux / macOS):** Jika muncul error *Permission Denied* pada folder log atau cache, berikan akses write dengan perintah:

```bash
chmod -R 775 storage bootstrap/cache

```

* **Optimasi Cache:** Jika Anda melakukan perubahan pada `.env` namun tidak terbaca, bersihkan cache aplikasi:

```bash
php artisan config:clear
php artisan cache:clear

```

```

```
