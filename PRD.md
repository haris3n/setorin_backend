# Product Requirements Document (PRD)

## Setor.in — Aplikasi Pengelolaan Bank Sampah Digital

| Field | Detail |
|---|---|
| **Nama Aplikasi** | Setor.in |
| **Versi** | 1.0 |
| **Tanggal** | 6 September 2026 |
| **Tech Stack** | Laravel 11 (PHP 8.2+), Laravel Sanctum, Spatie Permission, Filament v3, SQLite/MySQL |
| **Platform** | REST API (Backend), Web Admin Panel (Filament) |

---

## 1. Ringkasan Eksekutif

**Setor.in** adalah aplikasi digital pengelolaan bank sampah yang menghubungkan nasabah (masyarakat), petugas (lapangan), dan admin (pengelola). Nasabah menyetor sampah ke bank sampah, mendapatkan koin sebagai reward, yang dapat ditukarkan ke saldo dan ditarik secara tunai. Aplikasi ini bertujuan untuk:

- Mempermudah transaksi setor-menerima sampah
- Memberikan insentif finansial (koin & saldo) kepada masyarakat yang memilah sampah
- Menyediakan dashboard monitoring bagi admin dan petugas
- Mendorong kesadaran lingkungan melalui konten edukasi dan sistem misi (gamifikasi)

---

## 2. Pemodelan Pengguna (User Roles)

### 2.1 Nasabah
> Masyarakat umum yang menyetor sampah ke bank sampah.

- Mendaftar akun via OTP verifikasi
- Memilih bank sampah sebagai tempat setor
- Melihat harga sampah per jenis
- Melihat jadwal operasional bank sampah
- Melihat riwayat transaksi setoran
- Melaporkan rencana penyetoran sampah (informatif)
- Melihat saldo dan total koin
- Menukarkan koin ke saldo (1 Koin = Rp 10)
- Mengajukan penarikan saldo (min. Rp 10.000)
- Mengklaim misi untuk mendapatkan reward koin
- Membaca konten edukasi tentang pengelolaan sampah
- Mengelola profil dan melihat notifikasi

### 2.2 Petugas
> Staff lapangan yang bekerja di sebuah bank sampah.

- Melihat jadwal operasional bank sampah
- Memperbarui jam operasional bank sampah
- Membuat dan mengelola transaksi penyetoran nasabah
- Mengkonfirmasi selesainya transaksi
- Melihat laporan harian transaksi
- Melihat daftar nasabah di bank sampah-nya
- Melihat daftar harga sampah bank sampah-nya (read-only)
- Melihat log aktivitas pribadi

### 2.3 Admin
> Pengelola utama sistem.

- Mengelola seluruh pengguna (CRUD, Aktif/Nonaktif)
- Mengelola data bank sampah (CRUD, Aktif/Nonaktif)
- Mengelola harga sampah per bank sampah (CRUD)
- Mengelola harga konversi koin (Filament panel)
- Mengelola misi/quest (CRUD, Aktif/Nonaktif)
- Mengelola konten edukasi (CRUD + Publish/Archive workflow)
- Menyetujui atau menolak pengajuan penarikan saldo nasabah
- Melihat laporan transaksi global (statistik, grafik)
- Mengekspor data transaksi ke CSV
- Melihat log aktivitas admin
- Akses dashboard statistik (total nasabah, bank sampah aktif, transaksi bulanan, berat sampah terkumpul, penarikan pending)

---

## 3. Arsitektur Sistem

### 3.1 Diagram Relasi Entitas (ERD)

```
┌──────────────┐
│   pengguna   │──── 1:1 ────► nasabah ──── 1:N ────► transaksi_penyetoran ── 1:N ──► detail_transaksi_sampah
│              │                 │                        │                                   │
│              │──── 1:1 ────► petugas ──── 1:N ────┘    └── BelongsTo ──► harga_sampah ◄──┘
│              │
│              │──── 1:1 ────► saldo ──── 1:N ────► penarikan_saldo
│              │
│              │──── 1:N ────► koin
│              │──── 1:N ────► notifikasi
│              │──── 1:N ────► klaim_misi ──── BelongsTo ──► misi
│              │──── 1:N ────► otp_verifikasi
│              │──── 1:N ────► konten_edukasi
│              │──── 1:N ────► aktivitas_admin
└──────────────┘

┌──────────────┐
│ bank_sampah  │──── 1:N ────► harga_sampah
│              │──── 1:N ────► jadwal_operasional
│              │──── 1:N ────► petugas
│              │──── 1:N ────► nasabah
│              │──── 1:N ────► transaksi_penyetoran
└──────────────┘

petugas ── 1:N ──► aktivitas_petugas
harga_coin (konfigurasi global)
```

### 3.2 Daftar Tabel Database

| Tabel | Fungsi |
|---|---|
| `pengguna` | Data pengguna (nasabah, petugas, admin) |
| `nasabah` | Data spesifik nasabah (bank terdaftar, tanggal gabung) |
| `petugas` | Data spesifik petugas (bank ditugaskan) |
| `bank_sampah` | Data lokasi bank sampah |
| `harga_sampah` | Daftar harga per jenis sampah per bank |
| `harga_coin` | Konfigurasi nilai tukar koin ke rupiah |
| `jadwal_operasional` | Jam buka/tutup bank sampah per hari |
| `saldo` | Saldo rupiah nasabah |
| `koin` | Log koin nasabah (sumber: transaksi/misi) |
| `transaksi_penyetoran` | Header transaksi setor sampah |
| `detail_transaksi_sampah` | Detail item per transaksi |
| `penarikan_saldo` | Pengajuan penarikan tunai |
| `misi` | Daftar misi/quest gamifikasi |
| `klaim_misi` | Log klaim misi oleh nasabah |
| `konten_edukasi` | Artikel edukasi pengelolaan sampah |
| `notifikasi` | Notifikasi untuk pengguna |
| `otp_verifikasi` | OTP registrasi/login |
| `aktivitas_admin` | Audit log aktivitas admin |
| `aktivitas_petugas` | Audit log aktivitas petugas |
| `exports` | Queue status ekspor file |
| `personal_access_tokens` | Sanctum API tokens |
| `permissions`, `roles`, ... | Spatie Permission tables |

---

## 4. Modul dan Fitur

### 4.1 Autentikasi & Otorisasi

| Endpoint | Method | Deskripsi |
|---|---|---|
| `/api/register` | POST | Registrasi nasabah baru (role default: nasabah, status: pending) |
| `/api/verify-otp` | POST | Verifikasi OTP 6 digit (aktifkan akun, buat record nasabah & saldo) |
| `/api/login` | POST | Login & dapatkan Bearer token |
| `/api/logout` | POST | Hapus token (protected) |

**Aturan Keamanan:**
- OTP berlaku 10 menit, format 6 digit
- Brute-force protection: maks. 5 percobaan login, lockout 15 menit
- Rate limiting diterapkan pada register, verify-otp, dan login
- API protected menggunakan Laravel Sanctum

### 4.2 Manajemen Profil Nasabah

| Endpoint | Method | Deskripsi |
|---|---|---|
| `/api/nasabah/profil` | GET | Lihat profil lengkap (dengan saldo, bank, koin) |
| `/api/nasabah/profil` | PUT | Update nama, telepon, alamat |
| `/api/nasabah/notifikasi` | GET | Daftar notifikasi (auto-mark dibaca) |
| `/api/nasabah/edukasi` | GET | Daftar konten edukasi published |

### 4.3 Transaksi Penyetoran Sampah

| Endpoint | Method | Deskripsi |
|---|---|---|
| `/api/nasabah/bank-sampah` | GET | Lihat bank sampah aktif + jadwal + harga |
| `/api/nasabah/bank-sampah/pilih` | POST | Pilih bank sampah utama |
| `/api/nasabah/transaksi` | GET | Riwayat transaksi nasabah |
| `/api/nasabah/laporan-sampah` | POST | Laporkan rencana setor (informatif) |
| `/api/petugas/transaksi` | GET | Daftar transaksi di bank petugas |
| `/api/petugas/transaksi` | POST | Buat transaksi penyetoran baru |
| `/api/petugas/transaksi/{id}/konfirmasi` | PATCH | Konfirmasi transaksi selesai |

**Alur Transaksi (Petugas):**
1. Petugas memilih nasabah
2. Petugas input detail sampah (jenis + berat)
3. Sistem hitung subtotal per item: `harga_per_kg × berat_kg`
4. Sistem hitung total koin: `total_subtotal ÷ 100` (Rp 100 = 1 Koin)
5. Koin dikreditkan ke akun nasabah
6. Status transaksi: `diproses` → `selesai`
7. Notifikasi dikirim ke nasabah

### 4.4 Saldo & Penarikan

| Endpoint | Method | Deskripsi |
|---|---|---|
| `/api/nasabah/saldo` | GET | Lihat saldo, total koin, riwayat penarikan |
| `/api/nasabah/saldo/tukar-koin` | POST | Tukar koin ke saldo (1 Koin = Rp 10) |
| `/api/nasabah/saldo/tarik` | POST | Ajukan penarikan saldo |
| `/api/admin/penarikan` | GET | Daftar pengajuan penarikan |
| `/api/admin/penarikan/{id}/setujui` | PATCH | Setujui penarikan (kurangi saldo) |
| `/api/admin/penarikan/{id}/tolak` | PATCH | Tolak penarikan (dengan alasan) |

**Aturan Penarikan:**
- Minimum penarikan: Rp 10.000
- Saldo harus mencukupi
- Status: `pending` → `disetujui` / `ditolak`
- Saat disetujui, saldo dikurangi secara permanen

### 4.5 Sistem Misi (Gamifikasi)

| Endpoint | Method | Deskripsi |
|---|---|---|
| `/api/nasabah/misi` | GET | Daftar misi aktif (dengan status sudah klaim) |
| `/api/nasabah/misi/{id}/klaim` | POST | Klaim reward misi |
| `/api/admin/misi` | CRUD | Kelola misi |

**Aturan Misi:**
- Misi memiliki periode aktif (`tgl_mulai` s/d `tgl_selesai`)
- Satu nasabah hanya bisa klaim satu misi satu kali
- Reward berupa koin yang langsung dikreditkan

### 4.6 Bank Sampah & Harga Sampah

| Endpoint | Method | Deskripsi |
|---|---|---|
| `/api/admin/bank-sampah` | CRUD | Kelola bank sampah |
| `/api/admin/harga-sampah` | CRUD | Kelola harga sampah per bank |

**Aturan:**
- Bank sampah dan harga sampah menggunakan soft delete (status: `nonaktif`)
- Setiap bank sampah memiliki daftar harga sendiri
- Harga sampah dapat difilter per bank

### 4.7 Konten Edukasi

| Endpoint | Method | Deskripsi |
|---|---|---|
| `/api/admin/konten-edukasi` | CRUD | Kelola artikel edukasi |
| `/api/admin/konten-edukasi/{id}/publish` | PUT | Publikasikan artikel |
| `/api/admin/konten-edukasi/{id}/archive` | PUT | Arsipkan artikel |
| `/api/nasabah/edukasi` | GET | Nasabah baca artikel published |

**Workflow Status:** `draft` → `published` → `archived`

**Kategori:** Sampah Organik, Sampah Anorganik, Daur Ulang, Lingkungan, Tips Praktis

### 4.8 Laporan & Ekspor

| Endpoint | Method | Deskripsi |
|---|---|---|
| `/api/admin/laporan` | GET | Statistik global + per bank |
| `/api/admin/laporan/export` | GET | Ekspor transaksi ke CSV (per bulan) |
| `/api/admin/laporan/export-detail` | GET | Ekspor detail item ke CSV |

**Statistik Tersedia:**
- Total nasabah aktif, petugas, bank sampah aktif
- Total transaksi, total berat (kg), total koin (bulanan & keseluruhan)
- Performa per bank sampah (jumlah transaksi + total berat)

### 4.9 Logging & Audit

- **Aktivitas Admin:** Setiap operasi CRUD oleh admin tercatat (create, update, delete, login, export) dengan data lama vs baru
- **Aktivitas Petugas:** Transaksi baru, jadwal diubah, profil diubah, login tercatat

---

## 5. Filament Admin Panel

### 5.1 Admin Panel

| Resource | Fungsi |
|---|---|
| `UserResource` | CRUD pengguna, filter by role/status |
| `TransaksiPenyetoranResource` | Daftar transaksi (read-only) + Export Excel |
| `HargaSampahResource` | CRUD harga sampah per bank |
| `HargaCoinResource` | Konfigurasi nilai tukar koin |
| `PenarikanSaldoResource` | Review & approve/reject penarikan |
| `MisiResource` | CRUD misi gamifikasi |
| `KontenEdukasiResource` | CRUD + Publish/Archive artikel |
| `BankSampahResource` | CRUD bank sampah |
| `AktivitasAdminResource` | Log aktivitas admin (read-only) |
| `Dashboard` | Widget statistik + grafik transaksi 7 hari |

### 5.2 Petugas Panel

| Resource | Fungsi |
|---|---|
| `TransaksiPenyetoranResource` | Buat & kelola transaksi di bank sendiri |
| `HargaSampahResource` | Lihat harga sampah bank sendiri (read-only) |
| `JadwalOperasionalResource` | Kelola jam operasional bank |
| `NasabahResource` | Lihat nasabah di bank sendiri (read-only) |
| `PenarikanSaldoResource` | Lihat penarikan nasabah bank sendiri (read-only) |
| `AktivitasPetugasResource` | Log aktivitas pribadi (read-only) |
| `ProfilPetugas` | Edit profil pribadi |
| `Dashboard` | Widget statistik (harian, bulanan, total) |

---

## 6. Aturan Bisnis (Ringkasan)

| No | Aturan | Nilai |
|---|---|---|
| 1 | Konversi Rupiah ke Koin | Rp 100 = 1 Koin |
| 2 | Konversi Koin ke Rupiah | 1 Koin = Rp 10 |
| 3 | Minimum penarikan | Rp 10.000 |
| 4 | Batas percobaan login | 5 kali |
| 5 | Durasi lockout login | 15 menit |
| 6 | Masa berlaku OTP | 10 menit |
| 7 | Panjang OTP | 6 digit |
| 8 | Status awal akun baru | `pending` (belum aktif) |
| 9 | Soft delete untuk bank & misi | `status = nonaktif` |
| 10 | Default role registrasi | `nasabah` |
| 11 | Format response API | `{ status: bool, message?: string, data?: mixed }` |

---

## 7. Endpoint API Lengkap

### Public (Tanpa Autentikasi)
| Method | Endpoint | Rate Limit |
|---|---|---|
| POST | `/api/register` | register |
| POST | `/api/verify-otp` | otp |
| POST | `/api/login` | login |

### Protected — Nasabah
| Method | Endpoint |
|---|---|
| POST | `/api/logout` |
| GET | `/api/nasabah/profil` |
| PUT | `/api/nasabah/profil` |
| GET | `/api/nasabah/notifikasi` |
| GET | `/api/nasabah/edukasi` |
| GET | `/api/nasabah/bank-sampah` |
| POST | `/api/nasabah/bank-sampah/pilih` |
| GET | `/api/nasabah/transaksi` |
| POST | `/api/nasabah/laporan-sampah` |
| GET | `/api/nasabah/saldo` |
| POST | `/api/nasabah/saldo/tukar-koin` |
| POST | `/api/nasabah/saldo/tarik` |
| GET | `/api/nasabah/misi` |
| POST | `/api/nasabah/misi/{id}/klaim` |

### Protected — Petugas
| Method | Endpoint |
|---|---|
| GET | `/api/petugas/transaksi` |
| POST | `/api/petugas/transaksi` |
| PATCH | `/api/petugas/transaksi/{id}/konfirmasi` |
| GET | `/api/petugas/jadwal` |
| PUT | `/api/petugas/jadwal/{id}` |
| GET | `/api/petugas/laporan` |

### Protected — Admin
| Method | Endpoint |
|---|---|
| GET/POST | `/api/admin/pengguna` |
| GET/PUT/DELETE | `/api/admin/pengguna/{id}` |
| GET/POST | `/api/admin/bank-sampah` |
| GET/PUT/DELETE | `/api/admin/bank-sampah/{id}` |
| GET/POST | `/api/admin/harga-sampah` |
| GET/PUT/DELETE | `/api/admin/harga-sampah/{id}` |
| GET/POST | `/api/admin/misi` |
| GET/PUT/DELETE | `/api/admin/misi/{id}` |
| GET | `/api/admin/penarikan` |
| PATCH | `/api/admin/penarikan/{id}/setujui` |
| PATCH | `/api/admin/penarikan/{id}/tolak` |
| GET | `/api/admin/laporan` |
| GET | `/api/admin/laporan/export` |
| GET | `/api/admin/laporan/export-detail` |
| GET/POST | `/api/admin/konten-edukasi` |
| GET/PUT/DELETE | `/api/admin/konten-edukasi/{id}` |
| PUT | `/api/admin/konten-edukasi/{id}/publish` |
| PUT | `/api/admin/konten-edukasi/{id}/archive` |

---

## 8. Dependensi Teknis

### PHP Packages
| Package | Versi | Fungsi |
|---|---|---|
| `laravel/framework` | ^11.0 | Core framework |
| `laravel/sanctum` | ^4.0 | API token authentication |
| `filament/filament` | ^3.2 | Admin panel |
| `spatie/laravel-permission` | ^6.0 | Role & permission management |

### Infrastructure
| Component | Keterangan |
|---|---|
| PHP | ^8.2 |
| Database | SQLite (development) / MySQL (production) |
| Queue | Laravel default queue driver |
| Containerization | Docker + Docker Compose |
| Asset Bundling | Vite |

---

*Dokumen ini dibuat berdasarkan analisis kode sumber Setor.in Backend per 6 September 2026.*
