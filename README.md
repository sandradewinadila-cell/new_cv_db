# 🌌 CV Dinamis Berbasis Web - Nadila Sandra Dewi

[![PHP Version](https://img.shields.io/badge/PHP-8.x%20Native-777bb4?style=for-the-badge&logo=php&logoColor=white)](https://www.php.net/)
[![Database](https://img.shields.io/badge/MySQL-InnoDB-4479a1?style=for-the-badge&logo=mysql&logoColor=white)](https://www.mysql.com/)
[![Design](https://img.shields.io/badge/Theme-LIVA%20UX%20Specialist-c084fc?style=for-the-badge&logo=css3&logoColor=white)](assets/css/theme.css)
[![PDF Generation](https://img.shields.io/badge/Export-html2pdf.js-f472b6?style=for-the-badge&logo=javascript&logoColor=white)](https://github.com/eKoopmans/html2pdf.js)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg?style=for-the-badge)](LICENSE)

Aplikasi Curriculum Vitae (CV) digital interaktif, elegan, dan dinamis berbasis web untuk **Nadila Sandra Dewi** (Siswa Rekayasa Perangkat Lunak, SMK Negeri 1 Dlanggu). Dibangun menggunakan **PHP Native** dan basis data **MySQL**, aplikasi ini memadukan tampilan publik bergaya _LIVA UX Specialist_ (Dark Theme, Glassmorphism, Gradient Ungu-Pink) dengan panel manajemen konten (CMS) administrator yang lengkap, stabil, dan aman.

---

## 📸 Pratinjau Antarmuka (Screenshots)

|          Halaman Publik (Dark Theme)           |            Dashboard Administrator             |
| :--------------------------------------------: | :--------------------------------------------: |
| ![Public CV](assets/screenshots/public_cv.png) | ![Dashboard](assets/screenshots/dashboard.png) |

---

## ✨ Fitur Utama

- 🎨 **Desain Modern & Glassmorphism (Dark Theme)**: Mengadopsi design tokens khusus berbasis CSS Custom Properties (`theme.css`) dengan perpaduan latar belakang gelap (`#0a0118`), efek frosted-glass (`backdrop-filter: blur(20px)`), dan aksen gradasi ungu-pink yang futuristik.
- 🖼️ **Sistem Unggah Berkas & Gambar Stabil**:
  - **Foto Profil (Avatar)** di modul Biodata dengan preview lingkaran dinamis.
  - **Logo Institusi Pendidikan** dengan thumbnail di daftar riwayat sekolah.
  - **Foto/Cover Portofolio Proyek** (wajib unggah saat data baru).
  - **Berkas / Sertifikat Lisensi** (opsional) dengan integrasi tautan kredensial.
  - Dilengkapi validasi ekstensi (`jpg`, `jpeg`, `png`, `webp`), batas ukuran berkas (2MB), pembersihan berkas lama otomatis (`unlink()`), dan _fallback error handling_ (`onerror`).
- 🖨️ **Ekspor PDF Siap Cetak (Print-Friendly)**: Konversi instan dokumen CV langsung ke format A4 menggunakan `html2pdf.js`. Dilengkapi aturan CSS media cetak khusus: warna background otomatis berubah menjadi putih bersih, seluruh foto tetap tercetak rapi, dan elemen navigasi/tombol disembunyikan otomatis (`.no-print`).
- 👁️ **Toggle Show/Hide Password**: Fitur interaktif ikon mata pada form login untuk mengubah tipe input kata sandi antara _text_ dan _password_ secara halus.
- 🚪 **Popup Konfirmasi Logout**: Pengamanan dialog JavaScript konfirmasi sebelum administrator keluar dari sesi aktif.
- 🧭 **Sidebar + Topbar Responsif Admin**: Navigasi panel terpadu dengan penanda menu aktif otomatis (`.sidebar-item.active`) serta tata letak adaptif desktop & mobile.
- 🔒 **Proteksi Sesi & Keamanan Data**:
  - Middleware proteksi rute (`includes/auth.php`) mencegah akses liar ke file admin tanpa login aktif.
  - Menggunakan algoritma enkripsi modern `password_hash` & `password_verify` (Bcrypt).
  - Seluruh query database menerapkan **PDO Prepared Statements** untuk mencegah eksploitasi _SQL Injection_.
  - Sanitasi `htmlspecialchars()` di seluruh output teks untuk mencegah ancaman _Cross-Site Scripting (XSS)_.

---

## 🛠️ Teknologi yang Digunakan

| Komponen            | Spesifikasi / Library                                  | Keterangan                                                 |
| :------------------ | :----------------------------------------------------- | :--------------------------------------------------------- |
| **Server Lokal**    | [Laragon](https://laragon.org/) (Apache 2.4, PHP 8.1+) | Lingkungan web server pengembangan lokal                   |
| **Bahasa Utama**    | PHP 8.x Native (PDO)                                   | Pemrosesan logika CRUD, autentikasi, dan routing modular   |
| **Basis Data**      | MySQL / MariaDB (InnoDB, `utf8mb4`)                    | Penyimpanan data relasional terstruktur                    |
| **Custom Styling**  | `assets/css/theme.css`                                 | Glassmorphism, Neon Glow, Gradient Engine, & CSS Variables |
| **CSS Utility**     | Bootstrap 5.3 CDN                                      | Utility grid layout dan breakpoint responsif               |
| **Ikonografi**      | FontAwesome 6.4.0 CDN                                  | Ikon navigasi, aksi, dan indikator status                  |
| **Tipografi**       | Google Fonts (Space Grotesk & Inter)                   | Font modern untuk judul tajam dan teks nyaman dibaca       |
| **Generator PDF**   | html2pdf.js v0.10.1 CDN                                | Library client-side HTML-to-PDF rendering                  |
| **Version Control** | Git & GitHub                                           | Manajemen versi kode sumber repositori                     |

---

## 📁 Struktur Folder Proyek

```text
new_cv_db/
│
├── config/
│   └── database.php           # Koneksi database MySQL menggunakan PDO
│
├── includes/
│   ├── auth.php               # Middleware proteksi sesi login admin
│   ├── upload_helper.php      # Fungsi validasi & pemrosesan upload gambar
│   ├── header.php             # Template sidebar navigasi & topbar admin
│   └── footer.php             # Template footer & script konfirmasi logout
│
├── assets/
│   ├── css/
│   │   ├── theme.css          # Tema utama: LIVA UX Specialist (Dark & Glassmorphism)
│   │   ├── style.css          # Styling tambahan & aturan cetak print
│   │   └── admin.css          # Styling pelengkap admin dashboard
│   ├── js/
│   │   ├── cv-export.js       # Konfigurasi & trigger ekspor PDF (html2pdf.js)
│   │   └── main.js            # Interaktivitas JavaScript pendukung
│   ├── screenshots/           # Tangkapan layar dokumentasi aplikasi
│   │   ├── public_cv.png
│   │   └── dashboard.png
│   └── uploads/               # Direktori penyimpanan berkas yang diunggah
│       ├── avatars/           # Foto profil pengguna
│       ├── education/         # Logo sekolah / instansi pendidikan
│       ├── projects/          # Foto cover portofolio proyek
│       └── certificates/      # Berkas/foto bukti sertifikat
│
├── sql/
│   ├── schema.sql             # Skema DDL tabel-tabel MySQL
│   └── seeder.sql             # Data seeder profil awal & akun admin
│
├── index.php                  # Halaman publik CV interaktif & ekspor PDF
├── login.php                  # Halaman login admin dengan toggle password
├── process_login.php          # Verifikasi password hash & inisialisasi sesi
├── logout.php                 # Pembersihan sesi login admin
├── admin.php                  # Dashboard utama navigasi modul CRUD
├── admin_biodata.php          # CRUD data diri, kontak & foto profil
├── admin_education.php        # CRUD riwayat pendidikan & logo sekolah
├── admin_experience.php       # CRUD riwayat pengalaman kerja/organisasi
├── admin_skills.php           # CRUD keahlian teknis & soft skill
├── admin_projects.php         # CRUD proyek portofolio & upload cover
├── admin_certificates.php     # CRUD sertifikat & berkas kredensial
├── prd.md                     # Product Requirement Document (PRD)
└── README.md                  # Dokumentasi resmi proyek
```

---

## 🚀 Panduan Instalasi (Localhost via Laragon)

Ikuti langkah-langkah berikut untuk menjalankan aplikasi di komputer lokal Anda:

### 1. Klon Repositori

Buka terminal PowerShell atau Git Bash, lalu arahkan ke folder root web server Laragon (`www`):

```bash
cd C:\laragon\www
git clone https://github.com/sandradewinadila-cell/new_cv_db.git
```

### 2. Jalankan Layanan Laragon

Buka aplikasi **Laragon**, lalu klik tombol **Start All** untuk menyalakan modul **Apache** dan **MySQL**.

### 3. Buat dan Impor Database

1. Buka browser dan akses **phpMyAdmin** di [http://localhost/phpmyadmin](http://localhost/phpmyadmin).
2. Buat database baru bernama:
   ```sql
   new_cv_db
   ```
3. Buka tab **Import**, lalu impor file secara bertahap:
   - File pertama: `sql/schema.sql` (membuat struktur tabel).
   - File kedua: `sql/seeder.sql` (mengisi data default profil dan admin).

### 4. Konfigurasi Database (Jika Diperlukan)

Pengaturan default Laragon pada file `config/database.php` adalah:

- **Host:** `localhost`
- **Database:** `new_cv_db`
- **Username:** `root`
- **Password:** _(kosong)_

### 5. Akses Aplikasi

- **Halaman CV Publik:**  
  Akses di browser: [http://localhost/new_cv_db/](http://localhost/new_cv_db/)
- **Halaman Login Admin:**  
  Akses di browser: [http://localhost/new_cv_db/login.php](http://localhost/new_cv_db/login.php)

---

## 🔑 Kredensial Default Akun Admin

Untuk masuk dan mengelola konten CV melalui panel dashboard:

| Akun          | Kredensial                             |
| :------------ | :------------------------------------- |
| **URL Login** | `http://localhost/new_cv_db/login.php` |
| **Username**  | `admin`                                |
| **Password**  | `admin123`                             |

---

## 🗺️ Roadmap Pengembangan

- [x] **Fase 1: Core MVP & Visual Polish**
  - [x] Pembuatan skema database relasional & seeder data profil Nadila.
  - [x] Sistem autentikasi admin berbasis Session & Bcrypt hash.
  - [x] Panel CRUD lengkap (Biodata, Pendidikan, Pengalaman, Skill, Proyek, Sertifikat).
  - [x] Desain ulang antarmuka publik & admin dengan tema _LIVA UX Specialist_ (Glassmorphism & Dark Mode).
  - [x] Sistem upload gambar modular (Avatar, Logo, Cover Proyek, Sertifikat) dengan validasi & sanitasi berkas.
  - [x] Integrasi generator dokumen PDF format A4 via `html2pdf.js`.
- [ ] **Fase 2: Portabilitas Data & Keamanan Lanjut**
  - [ ] **Export to JSON**: Mengunduh seluruh data CV menjadi satu file format `.json` untuk kebutuhan cadangan data.
  - [ ] **Import from JSON**: Fitur restorasi dan sinkronisasi data CV dari file `.json`.
  - [ ] Penerapan CSRF Token di setiap formulir pengiriman data.
  - [ ] Multi-template switcher untuk variasi tampilan cetak CV.

---

## 📜 Lisensi

Proyek ini dilisensikan di bawah naungan **[MIT License](LICENSE)**. Anda dipersilakan untuk menggunakan, memodifikasi, dan mengembangkan kode sumber ini untuk keperluan pendidikan maupun portofolio personal.
